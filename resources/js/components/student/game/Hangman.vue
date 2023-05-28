<template>
    <div id="app" @keyup="handleKeyPress">
        <div class="container">
            <h4 v-if="isQuiz" class="text-center font-weight-bold">Level 1 - <span>Test {{ test }}</span></h4>
            <p id="quote" :class="{ 'strike': strikeout, 'highlight': puzzleComplete }">
                <span v-for="word in splitQuote" :key="word">
                    <template v-for="letter in word">{{ isRevealed(letter) }}</template>
                </span>
                <small v-if="gameOver">
                    —{{ quoteAuthor }}
                </small>
            </p>

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
            <button v-if="puzzleComplete" @click="nextTest" :class="{ 'highlight': gameOver }">Next - Test {{ test + 1 }}</button>

            <div class="status">
                <p>{{ message }}</p>
            </div>

            <button id="new-game" @click="newGame" :class="{ 'highlight': gameOver }">New game</button>
        </div>
    </div>
</template>

<script>
// Change this if you want the possibility of longer or shorter puzzles.
const maxLength = 40 // (Typically, the lower this number, the harder the puzzle.)

//Change this if you want more or fewer strikes allowed
const allowedStrikes = 3 //If you set this and maxLength both too high, the puzzle will be impossible to lose.

const defaultStrikes = new Array(allowedStrikes).fill({ icon: '⚪', guess: '' })

export default {
    data: () => ({
        letters: [],
        quotes: [], //Filled by the mounted hook
        currentQuote: '', //Filled by the mounted hook
        quoteAuthor: '',
        guesses: [],
        strikes: [...defaultStrikes],
        gameOver: false,
        disabled: false,
        words: [],
        test: 1,
        typingBalloon: {
            levelOne: [{test1: true, test2: false, test3: false}],
            levelTwo: [{test1: false, test2: false, test3: false}],
            levelThree: [{test1: false, test2: false, test3: false}],
            score: 0
        }
    }),
    mounted() {
        // this.fetchWord()
        this.setLetters()
        this.fetchWords()
    },
    methods: {
        //Can enter guesses with a keyboard, but it doesn't work super great because you need to be focusing a non-disabled element to use it currently. Needs some refinement.
        // fetchWord() {
        //     this.disabled = true
        //     fetch('https://random-word-api.herokuapp.com/word')
        //         .then((response) => response.json())
        //         .then((fetchedQuotes) => {
        //             this.quotes = fetchedQuotes
        //             this.pickAQuote()
        //             this.disabled = false
        //         })
        // },
        fetchWords() {
            axios.get('/storage/json/hangman.json')
                .then((response) => {
                    this.words = response.data
                    this.quotes = response.data.filter(item => {
                        return item.word.length == 3
                    })
                    this.pickAQuote()
                    this.disabled = false
                })
        },
        handleKeyPress(e) {
            const key = e.key.toUpperCase()
            if (key.length === 1 && key.match(/[a-zA-Z]/) && !this.guesses.includes(key)) {
                console.log(key)
                this.guess(key)
            }
        },
        pickAQuote() {
            const random = Math.floor(Math.random() * this.quotes.length)
            this.currentQuote = this.quotes[random].word.toUpperCase()
            this.quoteAuthor = this.quotes[random].author
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
            console.log(letter)
            this.guesses.push(letter)
            if (!this.currentQuote.includes(letter)) {
                this.strikes.pop()
                this.strikes = [{ key: Date.now(), icon: '🚫', guess: letter }, ...this.strikes]
            }
            if (this.strikeout || this.puzzleComplete) {
                this.gameOver = true
                if (this.puzzleComplete) fireEmAll();
            }
        },
        newGame() {
            const confirmation = confirm('End this game and start a new one?')
            if (!confirmation) return
            this.fetchWords()
            this.pickAQuote()
            this.guesses = []
            this.strikes = [...defaultStrikes]
            this.gameOver = false
        },
        nextTest() {
            this.test++
            this.fetchWords()
            this.pickAQuote()
            this.guesses = []
            this.strikes = [...defaultStrikes]
            this.gameOver = false
        },
        setLetters() {
            this.letters = [
                { letter: 'A', image: 'images/signs/A.png' },
                { letter: 'B', image: 'images/signs/B.png' },
                { letter: 'C', image: 'images/signs/C.png' },
                { letter: 'D', image: 'images/signs/D.png' },
                { letter: 'E', image: 'images/signs/E.png' },
                { letter: 'F', image: 'images/signs/F.png' },
                { letter: 'G', image: 'images/signs/G.png' },
                { letter: 'H', image: 'images/signs/H.png' },
                { letter: 'I', image: 'images/signs/I.png' },
                { letter: 'J', image: 'images/signs/J.png' },
                { letter: 'K', image: 'images/signs/K.png' },
                { letter: 'L', image: 'images/signs/L.png' },
                { letter: 'M', image: 'images/signs/M.png' },
                { letter: 'N', image: 'images/signs/N.png' },
                { letter: 'O', image: 'images/signs/O.png' },
                { letter: 'P', image: 'images/signs/P.png' },
                { letter: 'Q', image: 'images/signs/Q.png' },
                { letter: 'R', image: 'images/signs/R.png' },
                { letter: 'S', image: 'images/signs/S.png' },
                { letter: 'T', image: 'images/signs/T.png' },
                { letter: 'U', image: 'images/signs/U.png' },
                { letter: 'V', image: 'images/signs/V.png' },
                { letter: 'W', image: 'images/signs/W.png' },
                { letter: 'X', image: 'images/signs/X.png' },
                { letter: 'Y', image: 'images/signs/Y.png' },
                { letter: 'Z', image: 'images/signs/Z.png' },
            ]
        }
    },
    computed: {
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
    margin: 0 0 2rem;
    font-size: 1.25rem;
    line-height: 1.2em;
    background: lighten($lightBlue, 30%);
    border: 2px solid $darkGray;
    padding: 1rem 1rem 2rem;
    box-shadow: 4px 4px 0 0 $lightBlue;
    position: relative;

    @media(min-width: $breakpoint) {
        font-size: 2rem;
        margin: 1em 0 4rem;
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
</style>