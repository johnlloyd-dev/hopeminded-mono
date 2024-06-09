
<template>
    <body v-if="!disabledGame">
        <video autoplay="autoplay" muted loop id="myVideo">
            <source src="https://firebasestorage.googleapis.com/v0/b/hopeminded-d6a43.appspot.com/o/game-background%2Fgame7.mp4?alt=media&token=d9c5e13c-1c24-4a43-8032-467fd861647c" type="video/mp4">
        </video>
        <canvas id="canvas" ref="canvas"></canvas>
    </body>
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
import swal from 'sweetalert2'
import { mapGetters, mapActions } from 'vuex'

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
            array: [],
            data: [],
            y: -10,
            k: 0,
            h: 0,
            ct: 8,
            len: 0,
            score: 0,
            count: 0,
            flag: 0,
            text: "",
            show: false,
            char: "",
            chances: null,
            highestScore: 0,
            hideNextButton: false,
            cancelButtonText: "Cancel",
            nextButtonText: "Level 1",
            previousScore: 0,
            disabledGame: false,
            gameIsOver: false,
            isExit: false,
            theme: [],
            allThemes: [],
            selectColor: [
                "#DB291D",
                "#F85F68",
                "#77B1AD",
                "#F0B99A",
                "#E1CF79",
                "#F5998E",
                "#B16774",
                "#FAB301",
                "#F85F68",
                "#4FAA6D",
                "orange",
                "green",
                "#58A8C9",
                "#CDD6D5",
                "#ECD2A2",
                "white"
            ],
            c: "",
            ctx: "",
            typingBalloon: {
                highestLevel: 1,
                score: 0
            },
            strikes: [...defaultStrikes],
            quizMistake: {
                quiz_report_id: null,
                flag: null,
                attributes: {}
            }
        };
    },
    computed: {
        ...mapGetters({
            quizInfo: 'quizInfo'
        }),
        menuFlag() {
            return this.$store.state.menuFlag;
        },
        badGuesses() {
            return this.strikes.filter(s => s.guess).map(s => s.guess)
        },
        strikeout() {
            return this.badGuesses.length >= allowedStrikes
        },
    },
    mounted() {
        this.initQuizInfo()
    },
    beforeUnmount() {
        window.removeEventListener("keyup", this.handleKeyUp, true); // Succeeds
        this.$router.push('/student-textbook')
    },
    methods: {
        ...mapActions({
            getQuizInfo: 'getQuizInfo'
        }),
        async initQuizInfo() {
            this.disabledGame = true
            const data = await axios.get(`/api/quiz-reports/get?gameId=${2}`)
            if (data.data) {
                const record = data.data.report
                if (record.length == 0) {
                    this.chances = 2
                    this.highestScore = 0
                    this.disabledGame = true
                    swal.fire({
                        title: 'Remember',
                        text: `After taking this first quiz attempt, your score will be automatically recorded, and if you fail on this attempt, your teacher will decide regarding your retake. When the game starts, don't exit or refresh the browser. Happy playing.`,
                        confirmButtonText: `Got it!`,
                        icon: 'info',
                        width: 600,
                        padding: '3em',
                        backdrop: `
                            rgba(0,0,123,0.4)
                            url("https://sweetalert2.github.io/images/nyan-cat.gif")
                            left top
                            no-repeat
                        `
                    }).then((result) => {
                        if (result.value) {
                            this.disabledGame = false
                            this.storeQuizInfo()
                            this.fetchThemes()
                            window.addEventListener("keyup", this.handleKeyUp, true);
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
                                url("https://sweetalert2.github.io/images/nyan-cat.gif")
                                left top
                                no-repeat
                            `
                        }).then((result) => {
                            if (result.value) {
                                this.disabledGame = false
                                this.storeQuizInfo()
                                this.fetchThemes()
                                window.addEventListener("keyup", this.handleKeyUp, true);
                            }
                        })
                    }
                }
                // else if (record.length > 1) {
                //     this.chances = 'unli'
                //     const scores = record.map(data => {
                //         return data.total_score
                //     })
                //     this.highestScore = Math.max(...scores)
                //     if (record[1].mark == 'failed') {
                //         this.disabledGame = true
                //         this.show = true
                //         this.hideNextButton = true
                //         this.text = `Sorry. You already used up your play chances.`
                //         this.cancelButtonText = 'Exit'
                //     } else {
                //         this.disabledGame = false
                //         this.storeQuizInfo()
                //         this.fetchThemes()
                //         window.addEventListener("keyup", this.handleKeyUp, true);
                //     }
                // }
            }
        },
        storeQuizInfo() {
            axios.post(`/api/quiz/info/store/typing-balloon`, this.typingBalloon)
                .then(() => {
                    this.getQuizInfo()
                })
        },
        updateQuizInfo() {
            axios.post(`/api/quiz/info/update/${this.quizInfo.id}?gameId=2`, this.typingBalloon)
        },
        fetchThemes() {
            axios.get('json/games/balloon-game.json')
                .then(response => {
                    this.theme = response.data
                    this.allThemes = response.data
                    this.c = this.$refs.canvas;
                    this.ctx = this.c.getContext("2d");
                    this.c.height = window.innerHeight * 0.98;
                    this.c.width = window.innerWidth * 0.996;

                    const video = document.querySelector('#myVideo');

                    video.addEventListener('play', () => {
                        function drawFrame() {
                            if (!video.paused && !video.ended) {
                                ctx.drawImage(video, 0, 0, this.c.width, this.c.height);
                                requestAnimationFrame(drawFrame); // Use requestAnimationFrame for smoother animation
                            }
                        }
                        drawFrame();
                    });

                    this.initBalloons();
                    this.animate();
                })
                .catch(error => {
                    console.log(error);
                })
        },
        initBalloons() {
            this.data = []
            this.array = []
            if (this.flag == 0) {
                this.theme = _.shuffle(this.theme)
                this.theme = _.chunk(this.theme, 9)
            }
            if (this.flag == 1 || this.flag == 2) {
                this.y = -10
            }
            for (let i = 0; i < this.theme[this.flag].length; i++) {
                let x = Math.random() * (this.c.width - 60) + 20;
                let r = 100;
                this.y -= 200;
                let dy;
                switch (this.flag) {
                    case 0:
                        dy = (this.flag + 1);
                        break;
                    case 1:
                        dy = (this.flag + .1);
                        break
                    case 2:
                        dy = this.flag - .7;
                        break;
                }
                let color =
                    this.selectColor[
                    Math.floor(Math.random() * (this.selectColor.length - 1))
                    ];
                let url = this.theme[this.flag][i].image;
                let text = this.theme[this.flag][i].letter;
                this.data.push({
                    x: x,
                    y: this.y,
                    r: r,
                    color: color,
                    dy: dy,
                    text: text,
                    url: url,
                    word: ""
                });
            }
            this.array = this.data
        },
        animate() {
            requestAnimationFrame(this.animate);
            this.ctx.clearRect(0, 0, innerWidth, innerHeight);
            this.ctx.font = "bold 20px Century Gothic";
            this.ctx.fillStyle = "black";
            this.ctx.fillText(`Score: ${this.typingBalloon.score}`, 1300, 70);
            this.ctx.fillText(`Highest Score: ${this.highestScore}`, 100, 70);

            this.ctx.font = "bolder 25px Skranji";
            this.ctx.strokeStyle = "black";
            this.ctx.fillText(`Level ${this.flag + 1}`, 756, 100);

            this.ctx.font = "bold 20px Century Gothic";
            this.ctx.strokeStyle = "black";
            this.ctx.fillText(`Strikes: `, 100, 100);
            for (let m = 0; m < this.strikes.length; m++) {
                const x = (m+1)*25
                this.ctx.fillText(`${this.strikes[m].icon}`, (145 + x), 100);
            }

            // this.ctx.font = "25px Century Gothic";
            // this.ctx.strokeStyle = "black";
            // this.ctx.fillText(`Chances:`, 7, 25);

            this.ctx.beginPath();
            this.ctx.fillStyle = "lightgrey";
            this.ctx.fillRect(
                this.c.width / 2 - 10,
                this.c.height - 50,
                50,
                50
            );
            this.ctx.fill();

            // let img = new Image()
            // let img2 = new Image()
            // let img3 = new Image()
            // if (this.chances == 2) {
            //     img.src = 'images/red-heart.png';
            //     this.ctx.drawImage(img, 130, 5, 30, 30);

            //     img2.src = 'images/red-heart.png';
            //     this.ctx.drawImage(img2, 165, 5, 30, 30);
            // } else if(this.chances == 1) {
            //     img.src = 'images/red-heart.png';
            //     this.ctx.drawImage(img, 130, 5, 30, 30);

            //     img3.src = 'images/white-heart.png';
            //     this.ctx.drawImage(img3, 165, 5, 30, 30);
            // } else {
            //     this.ctx.font = "25px Century Gothic";
            //     this.ctx.fillStyle = "red";
            //     this.ctx.fillText(`Unlimited`, 130, 25);
            // }

            for (let m = 0; m < this.array.length; m++) {
                this.drawBalloon(this.array[m]);
            }
            for (let p = 0; p < this.array.length; p++) {
                this.updateBalloon(this.array[p]);
                if (this.array[p].y >= this.c.height) {
                    if (this.array[p].r > 0) {
                        this.count++
                        if (this.count == 1) {
                            this.data = []
                            this.array = []
                            this.ctx.clearRect(0, 0, innerWidth, innerHeight);
                            this.show = true
                            this.text = `Game Over. Your score is ${this.typingBalloon.score}.`
                            this.nextButtonText = 'Replay'
                            this.cancelButtonText = 'Exit'
                        }
                    }
                }
            }
        },
        drawBalloon(balloon) {
            this.ctx.beginPath();
            this.ctx.arc(balloon.x, balloon.y, balloon.r, 0, 2 * Math.PI);
            this.ctx.fillStyle = balloon.color;
            this.ctx.fill();
            this.ctx.stroke();
            this.ctx.font = "15px Arial";
            this.ctx.fillStyle = "white";
            let img = new Image()
            img.src = balloon.url;
            this.ctx.drawImage(img, balloon.x - 55, balloon.y - 75, 110, 160);
        },
        updateBalloon(balloon) {
            this.typeBalloon(balloon);
            balloon.y += balloon.dy;
        },
        typeBalloon(balloon) {
            this.ctx.font = "40px Arial";
            this.ctx.fillStyle = "black";
            this.ctx.fillText(this.char.toUpperCase(), 756, 713);
        },
        handleKeyUp(event) {
            this.char = ""
            let char = String.fromCharCode(event.keyCode).toLowerCase();
            this.char = char
            if (this.ct == 8) {
                for (let g = 0; g < this.array.length; g++) {
                    if (
                        this.array[g].y >= 0 &&
                        this.array[g].text.substring(0, 1) === char
                    ) {
                        this.h = g;
                        this.len = this.array[this.h].text.length;
                        break;
                    }
                }
            }

            const theme = this.allThemes
            const alphabetData = theme.find(data => {
                return data.letter === char
            })

            const answerAlphabet = char
            const answerImage = alphabetData.image

            if (this.array[this.h].text.substring(0, 1) === char) {
                let word = this.array[this.h].text.substring(0, 1);
                this.array[this.h].word = word;
                this.array[this.h].text = this.array[this.h].text.replace(word, "");
                this.ct--;
                if (this.array[this.h].text.length == 0) {
                    this.ct += this.len;
                    this.array[this.h].r = 0;
                    this.array[this.h].y = 0;
                    if (this.flag == 0) {
                        this.typingBalloon.score += 1;
                    } else if (this.flag == 1) {
                        this.typingBalloon.score += 2;
                    } else {
                        this.typingBalloon.score += 3;
                    }
                    this.array[this.h].word = "";
                    this.array[this.h].url = '';
                }
                this.strikes = [...defaultStrikes]
                this.updateQuizInfo()

                this.storeQuizMistakes(answerAlphabet, answerImage, 'correct')

                if (this.typingBalloon.score == (this.array.length + this.previousScore)) {
                    if (this.typingBalloon.score == 26) {
                        this.show = true
                        this.text = `Congratulations! You finished all 3 levels. Your score is ${this.typingBalloon.score}.`
                        this.nextButtonText = 'Replay'
                        this.cancelButtonText = 'Exit'
                    } else {
                        this.show = true
                        this.text = `You finished level ${this.flag + 1}. Proceed to level ${this.flag + 2}.`
                        this.nextButtonText = `Level ${this.flag + 2}`
                    }
                }
            } else {
                this.strikes.pop()
                this.strikes = [{ key: Math.floor(Math.random() * 100), icon: '🚫', guess: char }, ...this.strikes]

                this.storeQuizMistakes(answerAlphabet, answerImage, 'wrong')

                if (this.strikeout) {
                    this.data = []
                    this.array = []
                    this.ctx.clearRect(0, 0, innerWidth, innerHeight);
                    this.show = true
                    this.text = `Game Over. Your score is ${this.typingBalloon.score}.`
                    this.nextButtonText = 'Replay'
                    this.cancelButtonText = 'Exit'
                }
            }
        },
        nextLevel() {
            if (this.nextButtonText == 'Replay') {
                window.location.reload()
                return
            } else {
                this.flag++
                if (this.flag == 1) {
                    this.typingBalloon.highestLevel = 2
                } else if (this.flag == 2) {
                    this.typingBalloon.highestLevel = 3
                }
                this.updateQuizInfo()
            }
            this.show = false
            this.previousScore += this.typingBalloon.score
            this.strikes = [...defaultStrikes]
            this.initBalloons()
            this.animate();
        },
        cancelExit() {
            this.show = false
            window.removeEventListener("keyup", this.handleKeyUp, true); // Succeeds
            this.$router.push('/student-textbook')
        },
        storeQuizMistakes(answerAlphabet, answerImage, mark) {
            this.quizMistake.quiz_report_id = this.quizInfo.id
            this.quizMistake.flag = 'typing-balloon'
            const attributes = {
                answer: answerAlphabet,
                answer_image: answerImage,
                options: this.theme[this.flag],
                mark: mark
            }

            this.quizMistake.attributes = JSON.stringify(attributes)

            try {
                axios.post(`/api/quiz-mistake/store`, this.quizMistake)
            } catch (error) {
                console.log(error)
            }
        },
    }
};
</script>

<style lang="scss" scoped>
#canvas {
    border: 1px solid white;
}


@import url("https://fonts.googleapis.com/css2?family=Skranji&display=swap");

* {
    margin: 0;
    padding: 0;
}

body {
    background: linear-gradient(#ffffff, #7d7d7d);
    height: 100vh;
    width: 100vw;
}

#canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 1px solid black;
    background-size: cover;
    z-index: 999;
    // background-image: url("/images/background.jpg");
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
    z-index: 999;
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

video {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 1; /* Place it behind other content */
}
</style>
