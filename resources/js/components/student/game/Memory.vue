<template>
    <div id="overlay"></div>
    <div style="z-index: 9999">
        <div v-if="!disabledGame" class="body">
            <!-- <h3 class="fw-bold text-center w-100 mt-3">Timer: 00:{{ time }}</h3> -->
            <div class="row w-100">
                <div class="col-6">
                    <h4 class="text-left">Highest Score: {{ highestScore }}</h4>
                </div>
                <div class="col-6">
                    <h4 class="text-right">Score: {{ memoryGame.score }}</h4>
                </div>
            </div>
            <div id="app" class="cards-width">
                <div v-for="(card, index) in cards" :key="index"
                    :class="[{ 'down': card.down && !card.matched, 'up': !card.down, 'matched': card.matched }, ' card']"
                    v-on:click="handleClick(card)">
                    <img draggable="false" :src="card.image" />
                </div>
            </div>
        </div>
        <div v-else class="bg-white position-relative" style="height: 100vh">
            <div class="spinner-grow position-absolute top-50 start-50 translate-middle" style="width: 5rem; height: 5rem;"
                role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div v-show="show" id="container">
            <div class="container-inner">
                <div class="content">
                    <p>{{ text }}</p>
                </div>
                <div class="buttons">
                    <button @click="nextLevel()" v-if="!hideNextButton" type="button" class="confirm">{{ nextButtonText
                    }}</button>
                    <button @click="cancelExit()" type="button" class="cancel">{{ cancelButtonText }}</button>
                </div>
            </div>
        </div>
        <div class="modal fade" id="alphabetChoiceModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="alphabetChoiceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <h5 class="text-center">What sign represents the matched object?</h5>
                        <div class="row">
                            <div class="col-6 d-flex justify-content-center align-items-center">
                                <h1 style="font-size: 70px" v-if="Object.keys(matchedObject).length" class="fw-bold fst-italic">{{ matchedObject.icon.toUpperCase() }}</h1>
                            </div>
                            <div class="col-6">
                                <img v-if="Object.keys(matchedObject).length" style="width: 150px" :src="matchedObject.image" alt="">
                            </div>
                        </div>
                        <hr>
                        <div v-if="matched" class="row text-center">
                            <div class="status">
                                <h2>Strikes:</h2>
                                <ul class="status">
                                    <li v-for="strike in strikes" :key="strike">{{ strike.icon }}</li>
                                </ul>
                            </div>
                            <div v-for="option in alphabetOptions" :key="option.letter" class="col-6 mb-3">
                                <button :class="{'vibrating-element bg-danger': !letter_selection.mark && option.letter === letter_selection.letter, 'bg-success': letter_selection.mark && option.letter === letter_selection.letter}" @click="handleMatched(option.letter)" style="height: 200px; width: 200px" class="btn border">
                                    <img style="width: 100px;" :src="option.image">
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import swal from 'sweetalert2';
import { mapGetters, mapActions } from 'vuex'
// Duplicate elements of an array
const duplicate = (arr) => {
    return arr.concat(arr).sort()
};

// Check if two cards are a match
const checkMatch = (icons) => {
    if (icons[0] === icons[1]) {
        return true;
    }
};

//Change this if you want more or fewer strikes allowed
const allowedStrikes = 3 //If you set this and maxLength both too high, the puzzle will be impossible to lose.

const defaultStrikes = [];

for (let i = 0; i < allowedStrikes; i++) {
    const key = Math.floor(Math.random() * 100); // Generate a random number between 0 and 99

    defaultStrikes.push({
        key: key,
        icon: '⚪',
        guess: ''
    });
}

export default {
    data() {
        return {
            icons: [],
            cards: [],
            alphabets: [],
            flag: 0,
            show: false,
            runing: false,
            score: 0,
            matched: false,
            previousScore: 0,
            matchedObject: {},
            matchedData: [],
            chances: null,
            disabledGame: false,
            hideNextButton: false,
            cancelButtonText: "Cancel",
            nextButtonText: "Level 1",
            text: "",
            highestScore: 0,
            memoryGame: {
                highestLevel: 1,
                score: 0
            },
            letter_selection: {},
            container_style: {},
            strikes: [...defaultStrikes],
            time: 30, // Initial time in seconds
            timer: null // Timer reference
        }
    },
    beforeDestroy() {
        $('#alphabetChoiceModal').modal('hide')
        clearInterval(this.timer); // Clean up the timer before the component is destroyed
    },
    watch: {
        time(newValue, oldVlue) {
            if (newValue === 0) {
                this.gameOver()
            }
        }
    },
    methods: {
        ...mapActions({
            getQuizInfo: 'getQuizInfo'
        }),
        fetchData() {
            axios.get('json/memory-game.json')
                .then(response => {
                    this.icons = response.data
                    this.cardsShuffle()
                })
                .catch(error => {
                    console.log(error);
                })
        },
        fetchAlphabets() {
            axios.get('json/balloon-game.json')
                .then(response => {
                    this.alphabets = response.data
                })
                .catch(error => {
                    console.log(error);
                })
        },
        async initQuizInfo() {
            this.disabledGame = true
            const data = await axios.get(`/api/quiz-reports/get?gameId=${3}&flag=tutorial`)
            if (data.data) {
                const record = data.data
                if (record.length) {
                    const scores = record.map(data => {
                        return data.total_score
                    })
                    this.highestScore = Math.max(...scores)
                } else {
                    this.highestScore = 0
                }
                this.disabledGame = true
                swal.fire({
                    title: 'Hi Player',
                    text: `This is just a tutorial game. Happy playing.`,
                    confirmButtonText: `Got it!`,
                    icon: 'info',
                    width: 600,
                    padding: '3em',

                    backdrop: `
                        rgba(0,0,123,0.4)
                        url("https://sweetalert2.github.io/images/nyan-cat.gif)
                        left top
                        no-repeat
                    `
                }).then((result) => {
                    if (result.value) {
                        // this.startTimer();
                        this.disabledGame = false
                        this.storeQuizInfo()
                        this.fetchAlphabets()
                        this.fetchData();
                    }
                })
            }
        },
        // Create cards array based on icons, shuffle them
        cardsShuffle() {
            // prep objects
            this.icons = this.icons.slice(0, 12);
            this.cards = []
            this.cards = _.range(0, this.icons.length * 2)
            this.cards.forEach((card, index) => {
                this.cards[index] = {
                    icon: '',
                    image: '',
                    down: true,
                    matched: false
                }
            });
            const icons = this.icons
            icons.forEach((icon, index) => {
                this.cards[index].icon = icon.letter;
                this.cards[index + icons.length].icon = icon.letter;
                this.cards[index].image = icon.image
                this.cards[index + icons.length].image = icon.image;
            });
            this.cards = _.shuffle(this.cards);
        },
        handleClick(cardClicked) {
            console.log(cardClicked)
            if (!this.runing) {
                // turn card up
                if (!cardClicked.matched && this.cardCount.cardsUp < 2) {
                    cardClicked.down = false;
                }
                // when two cards are up, check if they match or turn them down
                if (this.cardCount.cardsUp === 2) {
                    this.runing = true;
                    setTimeout(() => {
                        let match = checkMatch(this.cardCount.icons);
                        this.cards.forEach((card) => {
                            if (match && !card.down && !card.matched) {
                                card.matched = true;
                            } else {
                                card.down = true;
                            }
                        });
                        if (match) {
                            this.matched = true
                            this.matchedData = cardClicked
                            this.matchedObject = this.matchedData
                            $('#alphabetChoiceModal').modal('show')
                        } else {
                            this.matched = false
                        }
                        this.runing = false;
                    }, 1000);
                }
            }
        },
        handleMatched(letter) {
            if(letter.toLowerCase() !== this.matchedData.icon.toLowerCase()) {
                this.strikes.pop()
                this.strikes = [{ key: Math.floor(Math.random() * 100), icon: '🚫', guess: letter }, ...this.strikes]
                this.letter_selection = { mark: 0, letter: letter }
            }

            if (this.strikeout) {
                $('#alphabetChoiceModal').modal('hide')
                this.show = true
                this.text = `Game Over. Your score is ${this.memoryGame.score}.`
                this.nextButtonText = 'Replay'
                this.cancelButtonText = 'Exit'
            } else {
                if (letter.toLowerCase() === this.matchedData.icon.toLowerCase()) {
                    this.letter_selection = { mark: 1, letter: letter }
                    this.cards.forEach((card) => {
                        if (this.matched && !card.down && !card.matched) {
                            card.matched = true;
                        } else {
                            card.down = true;
                        }
                    });
                    setTimeout(() => {
                        $('#alphabetChoiceModal').modal('hide')
                        this.matchedData = []
                        this.matched = false
                        this.letter_selection = {}
                        this.matchedObject = {}
                    }, 1000)
                    this.memoryGame.score++;
                    if (this.memoryGame.score == 16) {
                        clearInterval(this.timer);
                        this.show = true
                        this.text = `Congratulations! You've got the perfect score. Your score is ${this.memoryGame.score}.`
                        this.nextButtonText = 'Replay'
                        this.cancelButtonText = 'Exit'
                    } else {
                        this.updateQuizInfo()
                    }
                }
            }

        },
        storeQuizInfo() {
            axios.post(`/api/quiz/info/store/memory-game?flag=tutorial`, this.memoryGame)
                .then(() => {
                    this.getQuizInfo('tutorial')
                })
        },
        updateQuizInfo() {
            axios.post(`/api/quiz/info/update/${this.quizInfo.id}?gameId=3&flag=tutorial`, this.memoryGame)
        },
        nextLevel() {
            if (this.nextButtonText == 'Replay') {
                window.location.reload()
                return
            }
            this.show = false
        },
        cancelExit() {
            this.show = false
            this.$router.push('/student-dashboard')
        },
        startTimer() {
            this.timer = setInterval(() => {
                if (this.time > 0) {
                    this.time--;
                } else {
                    clearInterval(this.timer);
                    // Timer has reached 0, you can trigger any action here
                }
            }, 1000); // Timer tick every second (1000ms)
        },
        gameOver() {
            this.show = true
            this.text = `Game Over. Out of strikes. Your score is ${this.memoryGame.score}.`
            this.nextButtonText = 'Replay'
            this.cancelButtonText = 'Exit'
        }
    },
    mounted() {
        this.initQuizInfo()
    },
    computed: {
        ...mapGetters({
            quizInfo: 'quizInfo'
        }),
        // make a count of cards up and cards matched, keep icons of cards to check in array
        cardCount: function () {
            let cardUpCount = 0;
            let cardMatchedCount = 0;
            let icons = [];
            this.cards.forEach((card) => {
                if (!card.down && !card.matched) {
                    cardUpCount++;
                    icons.push(card.icon);
                }
                if (card.matched) {
                    cardMatchedCount++;
                }
            });
            return {
                cardsUp: cardUpCount,
                cardsMatched: cardMatchedCount,
                icons: icons
            }
        },
        badGuesses() {
            return this.strikes.filter(s => s.guess).map(s => s.guess)
        },
        strikeout() {
            return this.badGuesses.length >= allowedStrikes
        },
        alphabetOptions() {
            if (this.matched) {
                let data = []
                let matchedLetter = this.matchedData.icon.toLowerCase()
                let selectedObject = this.alphabets.find((data) => { return data.letter === matchedLetter })
                let filterObjects = this.alphabets.filter((data) => { return data.letter !== matchedLetter })
                let newData = _.sampleSize(filterObjects, 3);
                data.push(selectedObject)
                newData.forEach((item) => {
                    data.push(item)
                })
                return _.shuffle(data)
            } else {
                return []
            }
        }
    }
}
</script>

<style lang="scss" scoped>
@import url("https://fonts.googleapis.com/css2?family=Skranji&display=swap");

body {
    background: linear-gradient(#ffffff, #7d7d7d);
    padding: 20px;
    font-family: Helvetica, Arial, Sans-Serif;
}

#app {
    display: grid;
    grid-template-columns: repeat(auto-fit, 100px);
    grid-auto-rows: 100px;
    justify-content: center;
    grid-gap: 40px;
    perspective: 800px;
    margin: 0 auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

#overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-image: url("/images/background.jpg");
    z-index: -1;
}

.cards-width {
    max-width: 1100px;
}

.cards-width-2 {
    max-width: 900px;
}

img {
    width: 100%;
}

@keyframes vibrate {
  0% { transform: translateX(0); }
  20% { transform: translateX(-2px) rotate(-1deg); }
  40% { transform: translateX(2px) rotate(1deg); }
  60% { transform: translateX(-2px) rotate(-1deg); }
  80% { transform: translateX(2px) rotate(1deg); }
  100% { transform: translateX(0); }
}

.vibrating-element {
  animation: vibrate 1s;
  animation-iteration-count: 1;
  transition: opacity 1s;
}

.victoryState {
    grid-column-start: 1;
    grid-column-end: -1;
    text-align: center;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card {
    background: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 5px;
    box-shadow: 5px 5px 0 0 #333;
    cursor: pointer;
    animation: flipUp .5s forwards;

    img {
        opacity: 1;
        transition: opacity .5s;
    }

    &.down {
        animation: flipDown .5s forwards;

        img {
            opacity: 0;
        }
    }

    &.matched {
        animation: matching .3s forwards;
        animation-name: pop;
        animation-duration: 0.3s;
        animation-timing-function: ease-out;
    }
}

@keyframes flipDown {
    0% {
        background: #fff;
        transform: rotateY(0deg);
        box-shadow: 5px 5px 0 0 #333;
    }

    100% {
        background: rgb(115, 132, 127);
        transform: rotateY(180deg);
        box-shadow: -5px 5px 0 0 #333;
    }
}

@keyframes flipUp {
    0% {
        background: rgb(115, 132, 127);
        transform: rotateY(180deg);
        box-shadow: -5px 5px 0 0 #333;
    }

    100% {
        background: #fff;
        transform: rotateY(0deg);
        box-shadow: 5px 5px 0 0 #333;
    }
}

@keyframes matching {
    0% {
        transform: scale(1);
        background: #ffffff;
    }

    50% {
        transform: scale(1.1);
    }

    100% {
        transform: scale(1);
        box-shadow: 5px 5px 0 0 lightpink;
    }
}

@keyframes pop {
    0% {
        transform: scale(1);
        background: #ffffff;
    }

    50% {
        transform: scale(1.1);
    }

    100% {
        transform: scale(1);
        box-shadow: 5px 5px 0 0 lightpink;
    }
}

.text-right {
    text-align: right;
    font-weight: bold;
    margin-right: 100px;
}

.text-left {
    text-align: left;
    font-weight: bold;
    margin-left: 100px;
}

#container {
    max-width: 100vw;
    height: 100vh;
    position: fixed;
    width: 100%;
    left: 50%;
    top: 0;
    transform: translate(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    background-size: calc(10 * 2px) calc(10 * 2px);
}

.container-inner {
    background: #a4363e;
    padding: 40px;
    border-radius: 30px;
    box-shadow: 5px 6px 0px -2px #620d15, -6px 5px 0px -2px #620d15,
        0px -2px 0px 2px #ee9191, 0px 10px 0px 0px #610c14,
        0px -10px 0px 1px #e66565, 0px 0px 180px 90px #0d2f66;
    width: 640px;
}

.content {
    font-family: "Skranji", cursive;
    background: radial-gradient(#fffbf3, #ffe19e);
    padding: 24px;
    box-sizing: border-box;
    border-radius: 20px 18px 20px 18px;
    box-shadow: 0px 0px 0px 6px #5e1e21, 0px 0px 8px 6px #84222b,
        inset 0px 0px 15px 0px #614506, 6px 6px 1px 1px #e66565,
        -6px 6px 1px 1px #e66565;
    text-align: center;

    p {
        font-size: 30px;
        padding: 40px;
        box-sizing: border-box;
        color: #461417;
    }
}

#heart {
    position: relative;
    width: 100px;
    height: 90px;
    margin-top: 10px;
}

#heart::before,
#heart::after {
    content: "";
    position: absolute;
    top: 0;
    width: 52px;
    height: 80px;
    border-radius: 50px 50px 0 0;
    background: red;
}

#heart::before {
    left: 50px;
    transform: rotate(-45deg);
    transform-origin: 0 100%;
}

#heart::after {
    left: 0;
    transform: rotate(45deg);
    transform-origin: 100% 100%;
}

.buttons {
    margin-top: 40px;
    display: flex;
    justify-content: normal;
    align-items: center;
    gap: 30px;
    box-sizing: border-box;

    button {
        padding: 20px;
        flex: 1;
        border-radius: 20px;
        border: 2px solid #49181e;
        font-family: "Skranji", cursive;
        color: #fff;
        font-size: 32px;
        text-shadow: 1px 2px 3px #000000;
        cursor: pointer;

        &.confirm {
            background: linear-gradient(#ced869, #536d1b);
            box-shadow: 0px 0px 0px 4px #7e1522, 0px 2px 0px 3px #e66565;

            &:hover {
                box-shadow: 0px 0px 0px 4px #7e1522, 0px 2px 0px 3px #e66565,
                    inset 2px 2px 10px 3px #4e6217;
            }
        }

        &.cancel {
            background: linear-gradient(#ea7079, #891a1a);
            box-shadow: 0px 0px 0px 4px #7e1522, 0px 2px 0px 3px #e66565;

            &:hover {
                box-shadow: 0px 0px 0px 4px #7e1522, 0px 2px 0px 3px #e66565,
                    inset 2px 2px 10px 3px #822828;
            }
        }
    }
}

.status {
    display: flex;
    flex-wrap: wrap;
    list-style-type: none;
    align-items: center;
    margin: 1rem 0;

    h2 {
        font-size: 1rem;
        margin: 0;
    }

    ul {
        display: flex;
        margin: 0;
        padding: 0;

        li {
            margin-left: 0.25em;
        }
    }

    p {
        font-size: 1rem;
        width: 100%;
        margin: 0;
    }
}
</style>
