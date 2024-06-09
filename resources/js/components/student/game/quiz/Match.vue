<template>
    <video autoplay muted loop id="myVideo">
        <source src="https://firebasestorage.googleapis.com/v0/b/hopeminded-d6a43.appspot.com/o/game-background%2Fgame5.mp4?alt=media&token=6e45b6bd-83f3-4061-9a5b-95ba9d802ea5" type="video/mp4">
    </video>
    <div v-if="isLoading">Loading...</div>
    <div v-else class="mx-5">
        <div class="row w-100 my-5">
            <div class="col-6">
                <h4 class="text-start fw-bold ms-4">Highest Score: {{ highestScore }}
                </h4>
            </div>
            <div class="col-6">
                <h4 class="text-end fw-bold me-4">Score: {{ matchingGame.score }}
                </h4>
            </div>
        </div>
        <div :class="optionClasses.container_class">
            <div class="mb-5">
                <div class="status">
                    <h2 class="fw-bold">Strikes:</h2>
                    <ul class="status">
                        <li v-for="strike in strikes" :key="strike">{{ strike.icon }}</li>
                    </ul>
                </div>
            </div>
            <div :class="optionClasses.row_class || ''" class="row">
                <div :class="optionClasses.grid_class" class="d-flex flex-column align-items-center"
                    v-for="element in matchA" :key="element.order">
                    <div :class="optionClasses.card_class" aria-hidden="true"
                        class="card card-body d-flex flex-row justify-content-center">
                        <img :class="optionClasses.image_class" :src="element.data.image" alt="Options" srcset="">
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <draggable tag="div" :class="optionClasses.row_class" class="row" :list="matchB" @change="log"
                    v-bind="dragOptions" :move="onMove" @start="isDragging = true" @end="isDragging = false">
                    <transition-group :name="'flip-list'">
                        <div :class="optionClasses.grid_class" class="d-flex flex-column align-items-center"
                            v-for="element in matchB" :key="element.order">
                            <div :class="optionClasses.card_class" style="cursor: pointer"
                                @click="element.fixed = !element.fixed" aria-hidden="true"
                                class="card card-body d-flex flex-row justify-content-center">
                                <img :class="optionClasses.image_class" :src="element.data.image" alt="Options"
                                    srcset="">
                            </div>
                        </div>
                    </transition-group>
                </draggable>
            </div>
        </div>
        <div class="submit-button text-center mt-5">
            <button @click="submit" type="button" class="game-button">SUBMIT</button>
        </div>
    </div>
    <div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered d-flex justify-content-center">
            <img v-for="(item, index) in mistakes" :key="index" class="me-5" width="200" src="/images/x-icon.png" alt="Wrong Answer">
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

import { VueDraggableNext } from "vue-draggable-next";
import swal from 'sweetalert2'
import { mapActions, mapGetters } from 'vuex'

export default {
    name: "Matching Game",
    components: {
        draggable: VueDraggableNext,
    },
    data: () => {
        return {
            level: 1,
            list2: [],
            strikes: [...defaultStrikes],
            editable: true,
            isDragging: false,
            delayedDragging: false,
            randomNumber: null,
            disabledGame: false,
            optionA: [],
            optionB: [],
            matchA: [],
            matchB: [],
            isLoading: false,
            highestScore: 0,
            matchingGame: {
                highestLevel: 1,
                score: 0
            },
            show: false,
            hideNextButton: false,
            nextButtonText: "Level 1",
            cancelButtonText: "Cancel",
            text: "",
            mainScore: 0,
            highestScore: 0,
            mistakes: 0,
            quizMistake: {
                quiz_report_id: null,
                flag: null,
                attributes: {}
            },
            flag: 0
        };
    },
    async created() {
        this.isLoading = true
    },
    mounted() {
        this.initQuizInfo()
    },
    computed: {
        ...mapGetters({
            quizInfo: 'quizInfo'
        }),
        dragOptions() {
            return {
                animation: 0,
                group: "description",
                disabled: !this.editable,
                ghostClass: "ghost"
            };
        },
        badGuesses() {
            return this.strikes.filter(s => s.guess).map(s => s.guess)
        },
        strikeout() {
            return this.badGuesses.length >= allowedStrikes
        },
        arrayNumber() {
            if (this.randomNumber) {
                let myFunc = num => Number(num);

                return Array.from(String(this.randomNumber), myFunc);
            }

            return [];
        },
        optionClasses() {
            let rowClass = null;
            let cardClass = null;
            let imageClass = null;
            let gridClass = null;
            let containerClass = null;

            if (this.level == 1) {
                gridClass = 'col-3';
                containerClass = 'game-container-1';
                cardClass = 'card-box-1 card-animation-1';
                imageClass = 'option-image-1';
                rowClass = 'd-flex justify-content-center';
            } else if (this.level == 2) {
                gridClass = 'col-1 me-4';
                imageClass = 'option-image-2';
                cardClass = 'card-box-2 card-animation-2';
                rowClass = 'd-flex justify-content-center';
                containerClass = 'game-container-2';
            } else {
                gridClass = 'col-1';
                imageClass = 'option-image-3';
                cardClass = 'card-box-3 card-animation-3';
                rowClass = 'd-flex justify-content-center';
                containerClass = 'game-container-3';
            }

            return {
                row_class: rowClass,
                card_class: cardClass,
                image_class: imageClass,
                grid_class: gridClass,
                container_class: containerClass
            }
        }
    },
    watch: {
        isDragging(newValue) {
            if (newValue) {
                this.delayedDragging = true;
                return;
            }

            this.$nextTick(() => {
                this.delayedDragging = false;
            });
        }
    },
    methods: {
        ...mapActions({
            getQuizInfo: 'getQuizInfo'
        }),
        async initQuizInfo() {
            this.disabledGame = true
            const data = await axios.get(`/api/quiz-reports/get?gameId=${4}`)
            if (data.data) {
                const record = data.data.report
                if (record.length == 0) {
                    this.disabledGame = true
                    swal.fire({
                        title: 'Remember',
                        text: `After taking this first quiz attempt, your score will be automatically recorded, and if you fail on this attempt, your teacher will decide regarding your retake. When the game starts, don't exit or refresh the browser. Happy playing.`,
                        confirmButtonText: `Got it!`,
                        icon: 'info',
                        width: 600,
                        padding: '3em',
                        backdrop: `
                            gray
                            url("https://sweetalert2.github.io/images/nyan-cat.gif")
                            left top
                            no-repeat
                        `
                    }).then(async (result) => {
                        if (result.value) {
                            this.disabledGame = false
                            this.storeQuizInfo()
                            await this.fetchMatchOptions();
                            await this.generateRandomNumber();
                            await this.generateOptions();
                        }
                    })
                } else if (record.length > 0) {
                    this.chances = 1
                    const retake = data.data.retake[0].allowed_retake
                    const scores = record.map(data => {
                        return data.total_score
                    })
                    this.highestScore = Math.max(...scores)
                    if (retake === 0) {
                        this.disabledGame = true
                        this.show = true
                        this.hideNextButton = true
                        this.text = `Sorry. You already used up your play chances.`
                        this.cancelButtonText = 'Exit'
                    } else {
                        this.disabledGame = true
                        swal.fire({
                            title: 'Remember',
                            text: `You failed to get the passing score on your previous attempt. Make sure to pass this one. When the game starts, don't exit or refresh the browser. Happy playing.`,
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
                        }).then(async (result) => {
                            if (result.value) {
                                this.disabledGame = false
                                this.storeQuizInfo()
                                await this.fetchMatchOptions();
                                await this.generateRandomNumber();
                                await this.generateOptions();
                            }
                        })
                    }
                }
            }
        },
        storeQuizInfo() {
            axios.post(`/api/quiz/info/store/matching-game`, this.matchingGame)
                .then(() => {
                    this.getQuizInfo()
                })
        },
        updateQuizInfo() {
            axios.post(`/api/quiz/info/update/${this.quizInfo.id}?gameId=4`, this.matchingGame)
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
            this.$router.push('/student-textbook')
        },
        log(event) {
            console.log(event);
        },
        onMove({ relatedContext, draggedContext }) {
            const relatedElement = relatedContext.element;
            const draggedElement = draggedContext.element;
            return (
                (!relatedElement || !relatedElement.fixed) && !draggedElement.fixed
            );
        },
        async fetchMatchOptions() {
            try {
                const { data } = await axios.get('json/games/matching-game.json');

                this.optionA = data.matchA;
                this.optionB = data.matchB;
            } catch (error) {
                console.log(error)
            }
        },
        async generateRandomNumber() {
            let min;
            let max;

            switch (this.level) {
                case 1:
                    min = 1000;
                    max = 9999;
                    this.randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
                    break;

                case 2:
                    min = 10000000;
                    max = 99999999;
                    this.randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
                    break;

                default:
                    min = 100000000000;
                    max = 999999999999;
                    this.randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
                    break;
            }
        },
        async generateOptions() {
            try {
                const data1 = await this.filterMatch('a');
                const data2 = await this.filterMatch('b');

                this.matchA = data1;
                this.matchB = data2;
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false;
            }
        },
        async filterMatch(option) {
            let options;

            switch (option) {
                case 'a':
                    options = this.optionA;
                    break;

                default:
                    options = this.optionB;
                    break;
            }

            if (this.arrayNumber.length) {
                const filteredData = this.arrayNumber.map((number, index) => {
                    const foundOption = options.find(option => parseInt(option.number) == number)

                    return { data: foundOption, order: index + 1, fixed: false };
                });

                return _.shuffle(filteredData);
            }

            return [];
        },
        submit() {
            const matchA = this.matchA.map(data => data.data.number);
            const matchB = this.matchB.map(data => data.data.number);

            if (_.isEqual(JSON.stringify(matchA), JSON.stringify(matchB))) {
                if (this.matchingGame.highestLevel < 3) {
                    this.matchingGame.highestLevel++
                }

                switch (this.level) {
                    case 1:
                        this.matchingGame.score += 4;
                        break;

                    case 2:
                        this.matchingGame.score += 8;
                        break;

                    default:
                        this.matchingGame.score += 12;
                        break;
                }

                this.updateQuizInfo();
                this.storeQuizMistakes('correct');

                if (this.matchingGame.score == 24) {
                    this.show = true
                    this.text = `Congratulations! You've got the perfect score. Your score is ${this.matchingGame.score}.`
                    this.nextButtonText = 'Replay'
                    this.cancelButtonText = 'Exit'
                } else {
                    this.show = true
                    this.text = `You finished level ${this.level}. Proceed to level ${this.level + 1}.`
                    this.nextButtonText = `Level ${this.level + 1}`
                }
            } else {
                this.strikes.pop()
                this.strikes = [
                    {
                        key: Math.floor(Math.random() * 100),
                        icon: '🚫',
                        guess: this.randomNumber
                    }, ...this.strikes
                ]

                this.storeQuizMistakes('wrong')

                if (this.mistakes >= 2) {
                    this.show = true
                    this.text = `Game Over. Your score is ${this.matchingGame.score}.`
                    this.nextButtonText = 'Replay'
                    this.cancelButtonText = 'Exit'
                } else {
                    this.mistakes++
                    $('#exampleModal').modal('show')
                }
            }
        },
        async storeQuizMistakes(mark) {
            this.quizMistake.quiz_report_id = this.quizInfo.id
            this.quizMistake.flag = 'matching-game'

            const attributes = {
                match_a: this.matchA.map(data=>data.data.number),
                match_b: this.matchB.map(data=>data.data.number),
                mark: mark
            }

            this.quizMistake.attributes = JSON.stringify(attributes)

            try {
                await axios.post(`/api/quiz-mistake/store`, this.quizMistake)
            } catch (error) {
                console.log(error)
            }
        },
        async nextLevel() {
            if (this.nextButtonText == 'Replay') {
                window.location.reload()
                return
            } else {
                this.level++
                this.mistakes = 0;
                this.strikes = [...defaultStrikes]
                this.show = false
                await this.generateRandomNumber();
                await this.generateOptions();
            }
        },
    },
};
</script>

<style lang="scss" scoped>
.card-box-1 {
    width: 150px;
    height: 150px;
}

.card-box-2 {
    width: 120px;
    height: 120px;
}

.card-box-3 {
    width: 100px;
    height: 100px;
}

.flip-list-move {
    transition: transform 0.5s;
}

.no-move {
    transition: transform 0s;
}

.ghost {
    opacity: 0.5;
    background: #c8ebfb;
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

.game-container-1 {
    margin-left: 300px;
    margin-right: 300px;
}

.game-container-2 {
    margin-left: 0px;
    margin-right: 0px;
}

.game-container-3 {
    margin-left: 0px;
    margin-right: 0px;
}

.option-image-1 {
    width: 110px
}

.option-image-2 {
    width: 80px
}

.option-image-3 {
    width: 50px
}

@import url('https://fonts.googleapis.com/css?family=Carter+One');

body {
    background-color: #111;
    text-align: center;
    padding: 30px;
    background-image: url(data:image/png;base64,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);
}

/*NOIZE for background*/
.game-button {
    position: relative;
    top: 0;
    cursor: pointer;
    text-decoration: none !important;
    outline: none !important;
    font-family: 'Carter One', sans-serif;
    font-size: 20px;
    line-height: 1.5em;
    letter-spacing: .1em;
    text-shadow: 2px 2px 1px #0066a2, -2px 2px 1px #0066a2, 2px -2px 1px #0066a2, -2px -2px 1px #0066a2, 0px 2px 1px #0066a2, 0px -2px 1px #0066a2, 0px 4px 1px #004a87, 2px 4px 1px #004a87, -2px 4px 1px #004a87;
    border: none;
    margin: 15px 15px 30px;
    background: repeating-linear-gradient(45deg, #3ebbf7, #3ebbf7 5px, #45b1f4 5px, #45b1f4 10px);
    border-bottom: 3px solid rgba(16, 91, 146, 0.5);
    border-top: 3px solid rgba(255, 255, 255, .3);
    color: #fff !important;
    border-radius: 8px;
    padding: 8px 15px 10px;
    box-shadow: 0 6px 0 #266b91, 0 8px 1px 1px rgba(0, 0, 0, .3), 0 10px 0 5px #12517d, 0 12px 0 5px #1a6b9a, 0 15px 0 5px #0c405e, 0 15px 1px 6px rgba(0, 0, 0, .3);
}

.game-button:hover {
    top: 2px;
    box-shadow: 0 4px 0 #266b91, 0 6px 1px 1px rgba(0, 0, 0, .3), 0 8px 0 5px #12517d, 0 10px 0 5px #1a6b9a, 0 13px 0 5px #0c405e, 0 13px 1px 6px rgba(0, 0, 0, .3);
}

.game-button::before {
    content: '';
    height: 10%;
    position: absolute;
    width: 40%;
    background: #fff;
    right: 13%;
    top: -3%;
    border-radius: 99px;
}

.game-button::after {
    content: '';
    height: 10%;
    position: absolute;
    width: 5%;
    background: #fff;
    right: 5%;
    top: -3%;
    border-radius: 99px;
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
    z-index: 10;
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

#myVideo {
  position: fixed;
  right: 0;
  bottom: 0;
  min-width: 100%;
  min-height: 100%;
  z-index: -1;
}

.card-animation-1 {
    transition: 1s box-shadow;
}

.card-animation-1:hover {
    box-shadow: 0 5px 35px 0px rgba(0,0,0,.1);
}

.card-animation-1:hover::before, .card-animation-1:hover::after {
  display: block;
  content: '';
  position: absolute;
  width: 150px;
  height: 150px;
  background: #FDA8CF;
  border-radius: 5px;
  z-index: -1;
  animation: 1s clockwise infinite;
}

.card-animation-1:hover:after {
  background: #F3CE5E;
  animation: 2s counterclockwise infinite;
}

.card-animation-2 {
    transition: 1s box-shadow;
}

.card-animation-2:hover {
    box-shadow: 0 5px 35px 0px rgba(0,0,0,.1);
}

.card-animation-2:hover::before, .card-animation-2:hover::after {
  display: block;
  content: '';
  position: absolute;
  width: 120px;
  height: 120px;
  background: #FDA8CF;
  border-radius: 5px;
  z-index: -1;
  animation: 1s clockwise infinite;
}

.card-animation-2:hover:after {
  background: #F3CE5E;
  animation: 2s counterclockwise infinite;
}

.card-animation-3 {
    transition: 1s box-shadow;
}

.card-animation-3:hover {
    box-shadow: 0 5px 35px 0px rgba(0,0,0,.1);
}

.card-animation-3:hover::before, .card-animation-3:hover::after {
  display: block;
  content: '';
  position: absolute;
  width: 100px;
  height: 100px;
  background: #FDA8CF;
  border-radius: 5px;
  z-index: -1;
  animation: 1s clockwise infinite;
}

.card-animation-3:hover:after {
  background: #F3CE5E;
  animation: 2s counterclockwise infinite;
}

@keyframes clockwise {
  0% {
    top: -5px;
    left: 0;
  }
  12% {
    top: -2px;
    left: 2px;
  }
  25% {
    top: 0;
    left: 5px;
  }
  37% {
    top: 2px;
    left: 2px;
  }
  50% {
    top: 5px;
    left: 0;
  }
  62% {
    top: 2px;
    left: -2px;
  }
  75% {
    top: 0;
    left: -5px;
  }
  87% {
    top: -2px;
    left: -2px;
  }
  100% {
    top: -5px;
    left: 0;
  }
}

@keyframes counterclockwise {
  0% {
    top: -5px;
    right: 0;
  }
  12% {
    top: -2px;
    right: 2px;
  }
  25% {
    top: 0;
    right: 5px;
  }
  37% {
    top: 2px;
    right: 2px;
  }
  50% {
    top: 5px;
    right: 0;
  }
  62% {
    top: 2px;
    right: -2px;
  }
  75% {
    top: 0;
    right: -5px;
  }
  87% {
    top: -2px;
    right: -2px;
  }
  100% {
    top: -5px;
    right: 0;
  }
}
</style>
