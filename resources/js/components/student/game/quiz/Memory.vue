<template>
    <div v-if="!disabledGame" class="body">
        <h4 class="text-right">Score: {{ memoryGame.score }}</h4>
        <h3 class="text-center" style="font-weight: bolder; font-family: 'Skranji', cursive;">Level {{ flag + 1 }}</h3>
        <div id="app" :class="flag == 0 ? 'cards-width-1' : 'cards-width-2'">
            <div v-for="(card, index) in cards" :key="index"
                :class="[{ 'down': card.down && !card.matched, 'up': !card.down, 'matched': card.matched }, ' card']"
                v-on:click="handleClick(card)">
                <img :src="card.image" />
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
        console.log("it's a match");
        return true;
    }
};

export default {
    data() {
        return {
            icons: [],
            cards: [],
            flag: 0,
            show: false,
            runing: false,
            score: 0,
            previousScore: 0,
            disabledGame: false,
            hideNextButton: false,
            cancelButtonText: "Cancel",
            nextButtonText: "Level 1",
            text: "",
            memoryGame: {
                highestLevel: 1,
                score: 0
            }
        }
    },
    methods: {
        ...mapActions({
            getQuizInfo: 'getQuizInfo'
        }),
        fetchData() {
            axios.get('storage/json/memory-game.json')
                .then(response => {
                    this.icons = response.data
                    this.divideCards()
                    this.cardsShuffle()
                })
                .catch(error => {
                    console.log(error);
                })
        },
        async initQuizInfo() {
            this.disabledGame = true
            const data = await axios.get(`/api/quiz-reports/get?gameId=${3}`)
            if (data.data) {
                const record = data.data
                if (record.length == 0) {
                    this.disabledGame = true
                    swal.fire({
                        title: 'Remember',
                        text: `If you have played levels 1 and 2 in your first attempt but failed to get the passing score, you will be given another chance to play the game. But if you reach level 3 but failed to get the passing score, you will not be given another chance to play this game. When the game starts, don't exit or refresh the browser. Happy playing.`,
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
                            this.disabledGame = false
                            this.storeQuizInfo()
                            this.fetchData();
                        }
                    })
                } else if (record.length == 1) {
                    if (record[0].mark == 'failed' && record[0].highest_level == 3) {
                        this.disabledGame = true
                        this.show = true
                        this.hideNextButton = true
                        this.text = `Sorry. You already used up your play chances.`
                        this.cancelButtonText = 'Exit'
                    } else {
                        this.disabledGame = true
                        swal.fire({
                            title: 'Remember',
                            text: `You failed to get the passing score on your first attempt. This will be your final chance. When the game starts, don't exit or refresh the browser. Happy playing.`,
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
                                this.disabledGame = false
                                this.storeQuizInfo()
                                this.fetchData();
                            }
                        })
                    }
                } else if (record.length > 1) {
                    if (record[1].mark == 'failed') {
                        this.disabledGame = true
                        this.show = true
                        this.hideNextButton = true
                        this.text = `Sorry. You already used up your play chances.`
                        this.cancelButtonText = 'Exit'
                    } else {
                        this.disabledGame = false
                        this.storeQuizInfo()
                        this.fetchData();
                    }
                }
            }
        },
        // Create cards array based on icons, shuffle them
        cardsShuffle() {
            // prep objects
            this.cards = []
            this.cards = _.range(0, this.icons[this.flag].length * 2)
            this.cards.forEach((card, index) => {
                this.cards[index] = {
                    icon: '',
                    image: '',
                    down: true,
                    matched: false
                }
            });
            const icons = this.icons[this.flag]
            icons.forEach((icon, index) => {
                this.cards[index].icon = icon.letter;
                this.cards[index + icons.length].icon = icon.letter;
                this.cards[index].image = icon.image
                this.cards[index + icons.length].image = icon.image;
            });
            this.cards = _.shuffle(this.cards);
        },
        divideCards() {
            this.icons = _.shuffle(this.icons)
            const subArraySizes = [5, 9, 12];
            const dividedArrays = [];

            let currentIndex = 0;
            for (let i = 0; i < subArraySizes.length; i++) {
                const subArraySize = subArraySizes[i];
                const subArray = this.icons.slice(currentIndex, currentIndex + subArraySize);
                dividedArrays.push(subArray);
                currentIndex += subArraySize;
            }
            this.icons = dividedArrays
        },
        handleClick(cardClicked) {
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
                            if (this.flag == 0) {
                                this.memoryGame.score += 1;
                            } else if (this.flag == 1) {
                                this.memoryGame.score += 2;
                            } else if (this.flag == 2) {
                                this.memoryGame.score += 3;
                            }
                            if (this.memoryGame.score == 5 || this.memoryGame.score == 23) {
                                this.show = true
                                this.text = `You finished level ${this.flag + 1}. Proceed to level ${this.flag + 2}.`
                                this.nextButtonText = `Level ${this.flag + 2}`
                            } else if (this.memoryGame.score == 59) {
                                this.show = true
                                this.text = `Congratulations! You finished all 3 levels. Your score is ${this.memoryGame.score}.`
                                this.nextButtonText = 'Replay'
                                this.cancelButtonText = 'Exit'
                            }

                            this.updateQuizInfo()
                        }
                        this.runing = false;
                    }, 1000);
                }
            }
        },
        storeQuizInfo() {
            axios.post(`/api/quiz/info/store/memory-game`, this.memoryGame)
                .then(() => {
                    this.getQuizInfo()
                })
        },
        updateQuizInfo() {
            axios.post(`/api/quiz/info/update/${this.quizInfo.id}?gameId=3`, this.memoryGame)
        },
        nextLevel() {
            if (this.nextButtonText == 'Replay') {
                window.location.reload()
                return
            } else {
                this.flag++
                if (this.flag == 1) {
                    this.memoryGame.highestLevel = 2
                } else if (this.flag == 2) {
                    this.memoryGame.highestLevel = 3
                }
                this.updateQuizInfo()
            }
            this.show = false
            this.previousScore = 0
            this.previousScore += this.memoryGame.score
            this.cardsShuffle()
        },
        cancelExit() {
            this.show = false
            this.$router.push('/student-quiz')
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

.cards-width-1 {
    max-width: 700px;
}

.cards-width-2 {
    max-width: 900px;
}

img {
    width: 100%;
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
    margin-top: 20px;
    font-weight: bold;
    margin-right: 100px;
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
</style>