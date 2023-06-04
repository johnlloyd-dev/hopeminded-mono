<template>
    <div class="body">
        <div class="d-flex">
            <student-sidebar-component @collapse="onCollapse" />
            <div :class="collapsed ? 'collapsed-menu' : 'not-collapsed-menu'" class="main-content mx-5 w-100 h-100">
                <div class="d-flex justify-content-center">
                    <div class="card rounded-0 py-5 px-5" style="width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Quiz </h4>
                            <img src="/images/kids.png" alt="Kids" width="150">
                        </div>

                        <div class="card-body">
                            <div class="row d-flex justify-content-between mb-5">
                                <div class="col-lg-3 col-md-12 col-sm-12 p-0">
                                    <div class="card-sl" :class="{ 'opacity-75': !isMemoryGameAllowed || isProcessing }">
                                        <div class="card-image">
                                            <img src="/images/memory.png" />
                                        </div>
                                        <router-link to="/quiz-memory-game"
                                            :class="{ 'pointer-events-none': !isMemoryGameAllowed || isProcessing }"
                                            class="card-action"><i class="fa fa-play"></i></router-link>
                                        <div class="card-heading">
                                            Memory Game
                                            <h6>Alphabets/Letters</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-12 col-sm-12 p-0">
                                    <div class="card-sl" :class="{ 'opacity-75': !isTypingBalloonAllowed || isProcessing }">
                                        <div class="card-image">
                                            <img src="/images/balloon.png" />
                                        </div>
                                        <router-link to="/quiz-typing-balloon"
                                            :class="{ 'pointer-events-none': !isTypingBalloonAllowed || isProcessing }"
                                            class="card-action"><i class="fa fa-play"></i></router-link>
                                        <div class="card-heading">
                                            Typing Balloon
                                            <h6>Vowel/Consonants</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-12 col-sm-12 p-0">
                                    <div class="card-sl" :class="{ 'opacity-75': !isHangmanGameAllowed || isProcessing }">
                                        <div class="card-image">
                                            <img src="/images/hangman.png" />
                                        </div>
                                        <router-link to="/quiz-hangman-game"
                                            :class="{ 'pointer-events-none': !isHangmanGameAllowed || isProcessing }"
                                            class="card-action"><i class="fa fa-play"></i></router-link>
                                        <div class="card-heading">
                                            Hangman
                                            <h6>Alphabets/Words</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-warning" role="alert">
                                <p class="fw-bold"><span class="text-danger">Note:</span> Each of the game/quiz is linked to
                                    a specific textbook lesson. If a game is disabled, it means you haven't finished the
                                    corresponding lesson yet.</p>
                                <span class="fw-bold text-danger">Score Information</span>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <p class="text-dark fw-bold">Memory Game</p>
                                        <ul>
                                            <li>Perfect Score: <span class="fw-bold">59</span></li>
                                            <li>Passing Score: <span class="fw-bold">44</span></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-4">
                                        <p class="text-dark fw-bold">Typing Balloon</p>
                                        <ul>
                                            <li>Perfect Score: <span class="fw-bold">51</span></li>
                                            <li>Passing Score: <span class="fw-bold">38</span></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-4">
                                        <p class="text-dark fw-bold">Hangman Game</p>
                                        <ul>
                                            <li>Perfect Score: <span class="fw-bold">18</span></li>
                                            <li>Passing Score: <span class="fw-bold">10</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Tooltip } from 'bootstrap'
export default {
    data() {
        return {
            isProcessing: false,
            collapsed: false,
            isMemoryGameAllowed: false,
            isTypingBalloonAllowed: false,
            isHangmanGameAllowed: false
        }
    },
    methods: {
        onCollapse(isCollapsed) {
            if (isCollapsed) {
                this.collapsed = true
            } else {
                this.collapsed = false
            }
        },
        async getAlLeFlags() {
            this.isProcessing = true
            const flags = await axios.get(`/api/flags/alphabet-letters`)
            const attributes = JSON.parse(flags.data.attributes)
            this.isMemoryGameAllowed = Object.values(attributes).some(value => value == true);
            // this.isMemoryGameNotAllowed = Object.values(attributes).some(value => value == false);
        },
        async getVoCoFlags() {
            const flags = await axios.get(`/api/flags/vowel-consonants`)
            const attributes = JSON.parse(flags.data.attributes)
            this.isTypingBalloonAllowed = Object.values(attributes).some(value => value == true);
            // this.isTypingBalloonNotAllowed = Object.values(attributes).some(value => value == false);
        },
        async getAlWoFlags() {
            const flags = await axios.get(`/api/flags/alphabet-words`)
            const attributes = JSON.parse(flags.data.attributes)
            this.isHangmanGameAllowed = Object.values(attributes).some(value => value.length === 1);
            // this.isHangmanGameNotAllowed = Object.values(attributes).some(value => value.length === 0);
            this.isProcessing = false
        },
    },
    mounted() {
        this.getAlLeFlags()
        this.getVoCoFlags()
        this.getAlWoFlags()
        new Tooltip(document.body, {
            selector: "[data-bs-toggle='tooltip']",
        })
    }
}
</script>

<style scoped>
.body {
    background-image: url("/images/background.jpg");
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    color: #333;
    padding: 0;
    margin: 0;
    height: 100vh;
    overflow-x: hidden;
}

.card {
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
}

.card-title {
    font-weight: bold;
}

.card-sl {
    border-radius: 8px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    width: 250px;
    height: 270px;
}

.card-image {
    display: flex;
    justify-content: center;
}

.card-image img {
    width: 230px;
    height: 230px;
    border-radius: 8px 8px 0px 0;
}

.card-action {
    position: relative;
    float: right;
    margin-top: -25px;
    margin-right: 20px;
    z-index: 2;
    color: #E26D5C;
    background: #fff;
    border-radius: 50%;
    padding: 15px;
    font-size: 15px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 2px 0 rgba(0, 0, 0, 0.19);
}

.card-action:hover {
    color: #fff;
    background: #E26D5C;
    animation: pulse 1.5s infinite;
    -webkit-animation: pulse 1.5s infinite;
}

.card-heading {
    font-size: 18px;
    font-weight: bold;
    background: #fff;
    padding: 10px 15px;
    border-radius: 8px;
}

.card-text {
    padding: 10px 15px;
    background: #fff;
    font-size: 14px;
    color: #636262;
}

.card-button {
    display: flex;
    justify-content: center;
    padding: 10px 0;
    width: 100%;
    background-color: #1F487E;
    color: #fff;
    border-radius: 0 0 8px 8px;
}

.card-button:hover {
    text-decoration: none;
    background-color: #1D3461;
    color: #fff;

}

.pointer-events-none {
    pointer-events: none !important;
}

@keyframes pulse {
    0% {
        -moz-transform: scale(0.9);
        -ms-transform: scale(0.9);
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
    }

    70% {
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        -webkit-transform: scale(1);
        transform: scale(1);
        box-shadow: 0 0 0 50px rgba(90, 153, 212, 0);
    }

    100% {
        -moz-transform: scale(0.9);
        -ms-transform: scale(0.9);
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
        box-shadow: 0 0 0 0 rgba(90, 153, 212, 0);
    }
}

@-webkit-keyframes pulse {
    0% {
        -moz-transform: scale(0.9);
        -ms-transform: scale(0.9);
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
    }

    70% {
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        -webkit-transform: scale(1);
        transform: scale(1);
        box-shadow: 0 0 0 50px rgba(90, 153, 212, 0);
    }

    100% {
        -moz-transform: scale(0.9);
        -ms-transform: scale(0.9);
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
        box-shadow: 0 0 0 0 rgba(90, 153, 212, 0);
    }
}
</style>