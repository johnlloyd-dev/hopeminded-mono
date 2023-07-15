<template>
    <div id="overlay"></div>
    <div style="z-index: 9999">
        <div v-if="!disabledGame" id="app" @keyup="handleKeyPress">
            <h3 style="font-weight: bold;">Chances:
                <img v-if="chances == 2 || chances == 1" style="width: 30px" class="me-1" src="/images/red-heart.png"
                    alt="">
                <img v-if="chances == 2" style="width: 30px" class="me-1" src="/images/red-heart.png" alt="">
                <img v-if="chances == 1" style="width: 30px" src="/images/white-heart.png" alt="">
                <span v-if="chances == 'unli'" class="text-danger">Unlimited</span>
            </h3>
            <h4 v-if="isQuiz" style="font-weight: bolder; font-family: 'Skranji', cursive;" class="text-center mt-2">Level
                {{ flag + 1 }} - <span>Test {{ test + 1 }}</span></h4>
            <div class="row w-100">
                <div class="col-6">
                    <h4 class="text-start fw-bold ms-4">Highest Score: {{ highestScore }}
                    </h4>
                </div>
                <div class="col-6">
                    <h4 class="text-end fw-bold me-4">Score: {{ hangmanGame.score }}
                    </h4>
                </div>
            </div>
            <div class="mx-5 mt-4">
                <div class="row">
                    <div class="col-6">
                        <p id="quote" :class="{ 'strike': strikeout, 'highlight': puzzleComplete }">
                            <span v-for="word in splitQuote" :key="word">
                                <template v-for="letter in word">{{ isRevealed(letter) }}</template>
                            </span>
                            <small v-if="gameOver">
                                —{{ quoteAuthor }}
                            </small>
                        </p>
                    </div>
                    <div class="col-6">
                        <div v-if="flag == 0">
                            <div id="quote2" :class="{ 'strike': strikeout, 'highlight': puzzleComplete }">
                                <div class="col-lg-12 d-flex justify-content-center align-items-center">
                                    <img width="160" class="rounded" :src="currentData.image" alt="hangman image">
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div id="quote2" :class="{ 'strike': strikeout, 'highlight': puzzleComplete }">
                                <div class="row p-1">
                                    <div v-for="data in currentData.image" :key="data.image"
                                        class="d-flex justify-content-center align-items-center"
                                        :class="`col-${12 / currentData.image.length}`">
                                        <img width="160" height="110" class="rounded" :src="data" alt="hangman image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container z-50">
                <div class="status">
                    <h2>Strikes:</h2>
                    <ul class="status">
                        <li v-for="strike in strikes" :key="strike">{{ strike.icon }}</li>
                    </ul>
                </div>

                <div id="button-board">
                    <button v-for="letter in letters" @click="guess(letter.letter)" :key="letter.letter"
                        :class="{ 'strike': badGuesses.includes(letter.letter), 'highlight': guesses.includes(letter.letter) }"
                        :disabled="guesses.includes(letter.letter) || gameOver || disabled">
                        <img :src="letter.image" class="letter" :class="{ 'riser': guesses.includes(letter.letter) }" />
                        <span class="background"></span>
                    </button>
                </div>
                <button v-if="puzzleComplete" @click="nextTest" :class="{ 'highlight': gameOver }">Next - Test {{ test + 1
                }}</button>

                <div class="status">
                    <p>{{ message }}</p>
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
    </div>
</template>

<script>
import swal from 'sweetalert2'
import { mapActions, mapGetters } from 'vuex'
// Change this if you want the possibility of longer or shorter puzzles.
const maxLength = 40 // (Typically, the lower this number, the harder the puzzle.)

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
    data: () => ({
        letters: [],
        quotes: [], //Filled by the mounted hook
        currentQuote: '', //Filled by the mounted hook
        currentData: [],
        quoteAuthor: '',
        guesses: [],
        flag: 0,
        score: 0,
        show: false,
        hideNextButton: false,
        nextButtonText: "Level 1",
        cancelButtonText: "Cancel",
        text: '',
        strikes: [...defaultStrikes],
        gameOver: false,
        disabled: false,
        disabledGame: false,
        words: [],
        test: 0,
        chances: null,
        mainScore: 0,
        highestScore: 0,
        hangmanGame: {
            highestLevel: 1,
            score: 0
        }
    }),
    mounted() {
        this.initQuizInfo()
    },
    methods: {
        ...mapActions({
            getQuizInfo: 'getQuizInfo'
        }),
        async initQuizInfo() {
            this.disabledGame = true
            const data = await axios.get(`/api/quiz-reports/get?gameId=${1}`)
            if (data.data) {
                const record = data.data
                if (record.length == 0) {
                    this.chances = 2
                    this.highestScore = 0
                    this.disabledGame = true
                    swal.fire({
                        title: 'Remember',
                        text: `If you have played levels 1 and 2 in your first attempt but failed to get the passing score, you will be given another chance to play the game. But if you reach level 3 but failed to get the passing score, you will not be given another chance to play this game. When the game starts, don't exit or refresh the browser. Happy playing.`,
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
                    }).then((result) => {
                        if (result.value) {
                            this.disabledGame = false
                            this.storeQuizInfo()
                            this.setLetters()
                            this.fetchWords()
                        }
                    })
                } else if (record.length == 1) {
                    this.chances = 1
                    this.highestScore = record[0].total_score
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
                                gray
                                url("https://sweetalert2.github.io/images/nyan-cat.gif")
                                left top
                                no-repeat
                            `
                        }).then((result) => {
                            if (result.value) {
                                this.disabledGame = false
                                this.storeQuizInfo()
                                this.setLetters()
                                this.fetchWords()
                            }
                        })
                    }
                } else if (record.length > 1) {
                    this.chances = 'unli'
                    const scores = record.map(data => {
                        return data.total_score
                    })
                    this.highestScore = Math.max(...scores)
                    if (record[1].mark == 'failed') {
                        this.disabledGame = true
                        this.show = true
                        this.hideNextButton = true
                        this.text = `Sorry. You already used up your play chances.`
                        this.cancelButtonText = 'Exit'
                    } else {
                        this.disabledGame = false
                        this.storeQuizInfo()
                        this.setLetters()
                        this.fetchWords()
                    }
                }
            }
        },
        storeQuizInfo() {
            axios.post(`/api/quiz/info/store/hangman-game`, this.hangmanGame)
                .then(() => {
                    this.getQuizInfo()
                })
        },
        updateQuizInfo() {
            axios.post(`/api/quiz/info/update/${this.quizInfo.id}?gameId=1`, this.hangmanGame)
        },
        fetchWords() {
            axios.get('/storage/json/hangman-game.json')
                .then((response) => {
                    this.words = response.data.filter(item => {
                        return item.word.length != 3
                    })
                    this.divideCards()
                    this.pickAQuote()
                    this.disabled = false
                })
        },
        divideCards() {
            this.words = _.shuffle(this.words)
            const subArraySizes = [3, 6, 9];
            const dividedArrays = [];

            let currentIndex = 0;
            for (let i = 0; i < subArraySizes.length; i++) {
                const subArraySize = subArraySizes[i];
                const subArray = this.words.slice(currentIndex, currentIndex + subArraySize);
                switch (i) {
                    case 0:
                        dividedArrays.push(subArray);
                        break;
                    case 1:
                        const data = _.chunk(subArray, 2);
                        let words = ''
                        let images = []

                        let fullData = []
                        for (let index1 = 0; index1 < data.length; index1++) {
                            for (let index2 = 0; index2 < data[index1].length; index2++) {
                                words += data[index1][index2].word + ' ';
                                images.push(data[index1][index2].image)
                            }
                            fullData.push({ word: words, image: images })
                            words = ''
                            images = []
                        }
                        dividedArrays.push(fullData);
                        break;
                    case 2:
                        const data2 = _.chunk(subArray, 3);
                        let words2 = '';
                        let images2 = []

                        let fullData2 = []
                        for (let index1 = 0; index1 < data2.length; index1++) {
                            for (let index2 = 0; index2 < data2[index1].length; index2++) {
                                words2 += data2[index1][index2].word + ' ';
                                images2.push(data2[index1][index2].image)
                            }
                            fullData2.push({ word: words2, image: images2 })
                            words2 = ''
                            images2 = []
                        }
                        dividedArrays.push(fullData2);
                        break;
                }
                currentIndex += subArraySize;
            }
            this.quotes = dividedArrays
        },
        handleKeyPress(e) {
            const key = e.key.toUpperCase()
            if (key.length === 1 && key.match(/[a-zA-Z]/) && !this.guesses.includes(key)) {
                this.guess(key)
            }
        },
        pickAQuote() {
            this.currentData = []
            const random = Math.floor(Math.random() * this.quotes[this.flag].length)
            this.currentData = this.quotes[this.flag][this.test]
            this.currentQuote = this.quotes[this.flag][this.test].word.toUpperCase()
        },
        //The function that turns unguessed letters into blank spots
        isRevealed(letter) {
            if (!letter.match(/[a-zA-Z\s]/)) {
                return letter
            }
            return this.guesses.includes(letter) || this.gameOver ? letter : '_'
        },
        //Handles the guess and all possible results
        guess(letter) {
            this.guesses.push(letter)
            if (!this.currentQuote.includes(letter)) {
                this.strikes.pop()
                this.strikes = [{ key: Math.floor(Math.random() * 100), icon: '🚫', guess: letter }, ...this.strikes]
            }

            if (this.strikeout) {
                this.gameOver = true
                this.show = true
                this.text = `Game Over. Your score is ${this.hangmanGame.score}.`
                this.nextButtonText = 'Replay'
                this.cancelButtonText = 'Exit'
            }

            if (this.puzzleComplete) {
                this.gameOver = true
                this.score++
                this.setMainScore()

                if (this.score == 9) {
                    this.show = true
                    this.text = `Congratulations! You finished all 3 levels. Your score is ${this.hangmanGame.score}.`
                    this.nextButtonText = 'Replay'
                    this.cancelButtonText = 'Exit'
                } else {
                    if (this.test < 2) {
                        swal.fire({
                            text: `You finished test ${this.test + 1} of level ${this.flag + 1}. Proceed to test ${this.test + 2}.`,
                            showCancelButton: true,
                            confirmButtonText: `Test ${this.test + 2}`,
                            cancelButtonText: 'Exit',
                            icon: 'info',
                            width: 600,
                            padding: '3em',
                            backdrop: `
                                gray
                                url("https://sweetalert2.github.io/images/nyan-cat.gif")
                                left top
                                no-repeat
                            `
                        }).then((result) => {
                            if (result.value) {
                                this.nextTest()
                            } else if (result.dismiss === swal.DismissReason.cancel) {
                                window.location.href = '/student-textbook'
                                return
                            }
                        })
                    } else {
                        this.show = true
                        this.text = `You finished level ${this.flag + 1}. Proceed to level ${this.flag + 2}.`
                        this.nextButtonText = `Level ${this.flag + 2}`
                    }
                }
            }
        },
        newGame() {
            window.location.reload()
        },
        nextTest() {
            this.test++
            this.pickAQuote()
            this.guesses = []
            this.strikes = [...defaultStrikes]
            this.gameOver = false
        },
        nextLevel() {
            if (this.nextButtonText == 'Replay') {
                window.location.reload()
                return
            } else {
                this.flag++
                if ((this.flag + 1) == 1) {
                    this.hangmanGame.highestLevel == 1
                } else if ((this.flag + 1) == 2) {
                    this.hangmanGame.highestLevel == 2
                } else if ((this.flag + 1) == 3) {
                    this.hangmanGame.highestLevel == 3
                }
                this.updateQuizInfo()
                this.test = 0
                this.pickAQuote()
                this.guesses = []
                this.strikes = [...defaultStrikes]
                this.gameOver = false
            }
            this.show = false
        },
        cancelExit() {
            this.show = false
            this.$router.push('/student-textbook')
        },
        setMainScore() {
            if ((this.flag + 1) == 1) {
                this.hangmanGame.score += 1;
            } else if ((this.flag + 1) == 2) {
                this.hangmanGame.score += 2;
            } else if ((this.flag + 1) == 3) {
                this.hangmanGame.score += 3;
            }
            this.updateQuizInfo()
        },
        setLetters() {
            this.letters = [
                { letter: 'A', image: 'storage/hand-signs/A.png' },
                { letter: 'B', image: 'storage/hand-signs/B.png' },
                { letter: 'C', image: 'storage/hand-signs/C.png' },
                { letter: 'D', image: 'storage/hand-signs/D.png' },
                { letter: 'E', image: 'storage/hand-signs/E.png' },
                { letter: 'F', image: 'storage/hand-signs/F.png' },
                { letter: 'G', image: 'storage/hand-signs/G.png' },
                { letter: 'H', image: 'storage/hand-signs/H.png' },
                { letter: 'I', image: 'storage/hand-signs/I.png' },
                { letter: 'J', image: 'storage/hand-signs/J.png' },
                { letter: 'K', image: 'storage/hand-signs/K.png' },
                { letter: 'L', image: 'storage/hand-signs/L.png' },
                { letter: 'M', image: 'storage/hand-signs/M.png' },
                { letter: 'N', image: 'storage/hand-signs/N.png' },
                { letter: 'O', image: 'storage/hand-signs/O.png' },
                { letter: 'P', image: 'storage/hand-signs/P.png' },
                { letter: 'Q', image: 'storage/hand-signs/Q.png' },
                { letter: 'R', image: 'storage/hand-signs/R.png' },
                { letter: 'S', image: 'storage/hand-signs/S.png' },
                { letter: 'T', image: 'storage/hand-signs/T.png' },
                { letter: 'U', image: 'storage/hand-signs/U.png' },
                { letter: 'V', image: 'storage/hand-signs/V.png' },
                { letter: 'W', image: 'storage/hand-signs/W.png' },
                { letter: 'X', image: 'storage/hand-signs/X.png' },
                { letter: 'Y', image: 'storage/hand-signs/Y.png' },
                { letter: 'Z', image: 'storage/hand-signs/Z.png' },
            ]
        }
    },
    computed: {
        ...mapGetters({
            quizInfo: 'quizInfo'
        }),
        splitQuote() {
            return this.currentQuote.split(' ')
        },
        badGuesses() {
            return this.strikes.filter(s => s.guess).map(s => s.guess)
        },
        strikeout() {
            return this.badGuesses.length >= allowedStrikes
        },
        puzzleComplete() {
            return this.unrevealed === 0
        },
        levelComplete() {

        },
        unrevealed() {
            return [...this.currentQuote].filter(letter => {
                return letter.match(/[a-zA-Z]/) && !this.guesses.includes(letter)
            }).length
        },
        message() {
            if (!this.gameOver) {
                return '☝️ Pick a hand sign'
            } else if (this.strikeout) {
                return '❌ You lost this round. Try again?'
            } else if (this.puzzleComplete) {
                return '🎉 You win!'
            }
            //You can never be too safe ¯\_(ツ)_/¯
            return '😬 Unforeseen error state, maybe try a new game?'
        },
        isQuiz() {
            return window.localStorage.getItem('menuFlag') == "quiz" ? true : false
        }
    }
}

//Confetti! 🎉
//All the below code is just for the confetti. Could've brought it into Vue but didn't seem like there was any real reason to. Library is linked in the HTML tab's header settings.
let count = 200;
let defaults = {
    origin: { y: 0.5 },
    colors: ['#ffd100', '#a7a8aa', '#ff6a13', '#e4002b', '#7ba7bc', '#34657f']
};

const fire = (particleRatio, opts) => {
    confetti(
        Object.assign({}, defaults, opts, {
            particleCount: Math.floor(count * particleRatio)
        })
    );
};

const fireEmAll = () => {
    fire(0.25, {
        spread: 26,
        startVelocity: 55
    });
    fire(0.2, {
        spread: 60
    });
    fire(0.35, {
        spread: 100,
        decay: 0.91
    });
    fire(0.1, {
        spread: 120,
        startVelocity: 25,
        decay: 0.92
    });
    fire(0.1, {
        spread: 120,
        startVelocity: 45
    });
};
</script>

<style lang="scss" scoped>
$lighterGray: #d7dade;
$lightGray: #a7a8aa;
$darkGray: #53565a;
$mediumGray: #888b8d;
$black: #101820;
$white: #fff;
$yellow: #ffd100;
$orange: #ff6a13;
$red: #e4002b;
$lightBlue: #7ba7bc;
$darkBlue: #34657f;

$easing: cubic-bezier(0.5, 0, 0.5, 1);
$breakpoint: 600px;

*,
*:before,
*:after {
    box-sizing: border-box;
}

*:focus {
    outline: none;
    box-shadow: 0 0 0 4px $yellow;
}

html {
    font-size: 1.125em;
    line-height: 1.5;

    @media(min-width: 1200px) {
        font-size: 1.375em;
    }
}

body {
    margin: 0;
    min-height: 100vh;
    font-size: 1.5em;
    font-family: "Rubik Mono One", "Pathway Gothic One";
    color: $darkGray;
    background: lighten($lightBlue, 35%);
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

.container {
    width: 100%;
    margin: auto;
    padding: 1rem;

    @media(min-width: $breakpoint) {
        padding: 2rem;
    }
}

#quote {
    letter-spacing: 0.1em;
    // margin: 0 0 2rem;
    font-size: 1.25rem;
    line-height: 1.2em;
    background: lighten($lightBlue, 30%);
    border: 2px solid $darkGray;
    padding: 1rem 1rem 2rem;
    box-shadow: 4px 4px 0 0 $lightBlue;
    position: relative;

    @media(min-width: $breakpoint) {
        font-size: 2rem;
        // margin: 1em 0 4rem;
        padding: 2rem 2rem 3rem;
    }

    &.strike {
        color: $lighterGray;
        background-color: $red;
    }

    &.highlight {
        background-color: $yellow;
    }

    span {
        margin-right: 0.75em;
        display: inline-block;
    }

    small {
        font-size: .5em;
        position: absolute;
        bottom: 0.5em;
        left: 2em;
    }
}

#quote2 {
    background: lighten($lightBlue, 30%);
    border: 2px solid $darkGray;
    box-shadow: 4px 4px 0 0 $lightBlue;
    position: relative;
}

#button-board {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(4em, 1fr));
    grid-gap: 0.5rem;

    @media (min-width: $breakpoint) {
        grid-template-columns: repeat(auto-fit, minmax(4em, 1fr));
    }
}

button {
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: inherit;
    padding: 0.4em .5em 0.3em .5em;
    width: auto;
    line-height: 1;
    border: 2px solid $darkGray;
    color: inherit;
    background: lighten($lightBlue, 30%);
    font-size: .75em;
    box-shadow: 4px 4px 0 0 $lightBlue;
    position: relative;
    overflow: hidden;
    transform-origin: bottom right;

    @media(min-width: $breakpoint) {
        font-size: 1em;
    }

    .background {
        content: '';
        height: 100%;
        width: 100%;
        background: $yellow;
        position: absolute;
        z-index: 0;
        bottom: 0;
        left: 0;
        transform: scaleY(0);
        transform-origin: bottom;
        transition: transform .2s cubic-bezier(.75, 0, .25, 1);
    }

    &:hover:not(:active):not([disabled]),
    &:focus:not(:active):not([disabled]) {
        transform: translate(-1px, -1px);
        box-shadow: 6px 6px 0 0 $lightBlue;
    }

    &:not([disabled]):active {
        transform: translate(2px, 2px);
        box-shadow: 0 0 0 0 $lightBlue;
    }

    &.highlight .background {
        transform: scaleY(1);
    }

    &.strike {
        color: $lighterGray;

        .background {
            background-color: $red;
        }
    }

    .letter {
        position: relative;
        z-index: 2;
        color: inherit;
        width: 50px;
    }

    &[disabled]:not(.highlight) {
        border-color: $lightGray;

        .letter {
            color: $lightGray;
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

.riser {
    animation: rise .3s $easing;
    display: block;
}

@keyframes rise {
    0% {
        transform: translateY(0);
    }

    44.9% {
        transform: translateY(-1.5em);
    }

    45% {
        transform: translateY(1.5em);
    }

    55% {
        transform: translateY(1.5em);
    }

    0% {
        transform: translateY(0);
    }
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
</style>
