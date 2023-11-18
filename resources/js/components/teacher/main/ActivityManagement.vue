<template>
    <div class="body">
        <div class="d-flex">
            <teacher-sidebar-component @collapse="onCollapse" />
            <div :class="collapsed ? 'collapsed-menu' : 'not-collapsed-menu'" class="main-content mx-5 w-100 h-100">
                <div class="d-flex justify-content-center">
                    <div class="card rounded-0 py-5 px-5" style="width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Quiz and Skill Test Reports </h4>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" v-model="gameFlag" value="hangman-game" id="hangmanGame" autocomplete="off"
                                    :checked="gameFlag == 'hangman-game'">
                                <label class="btn btn-outline-dark fw-bold rounded-0"
                                    :class="gameFlag == 'hangman-game' ? 'text-white' : 'text-black'" for="hangmanGame">
                                    <h5 class="fw-bold">Alphabets/Words</h5>
                                    <h6 class="text-start">Hangman Game</h6>
                                </label>

                                <input type="radio" class="btn-check" v-model="gameFlag" value="typing-balloon" id="typingBalloon" autocomplete="off"
                                    :checked="gameFlag == 'typing-balloon'">
                                <label class="btn btn-outline-dark fw-bold rounded-0"
                                    :class="gameFlag == 'typing-balloon' ? 'text-white' : 'text-black'" for="typingBalloon">
                                    <h5 class="fw-bold">Vowels/Consonants</h5>
                                    <h6 class="text-start">Typing Balloon</h6>
                                </label>

                                <input type="radio" class="btn-check" v-model="gameFlag" value="memory-game" id="memoryGame" autocomplete="off"
                                    :checked="gameFlag == 'memory-game'">
                                <label class="btn btn-outline-dark fw-bold rounded-0"
                                    :class="gameFlag == 'memory-game' ? 'text-white' : 'text-black'" for="memoryGame">
                                    <h5 class="fw-bold">Alphabets/Letters</h5>
                                    <h6 class="text-start">Memory Game</h6>
                                </label>
                            </div>
                        </div>
                        <Statistics :game-flag="gameFlag"></Statistics>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Quiz Answer Keys </h4>
                        </div>
                        <div class="main-content mt-3">
                            <div class="mb-3">
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" v-model="keyFlag" :value="0" id="btnradio1"
                                        autocomplete="off" :checked="keyFlag == 0">
                                    <label class="btn btn-outline-dark fw-bold rounded-0"
                                        :class="keyFlag == 0 ? 'text-white' : 'text-black'"
                                        for="btnradio1">Hand Signs</label>

                                    <input type="radio" class="btn-check" v-model="keyFlag" :value="1" id="btnradio2"
                                        autocomplete="off" :checked="keyFlag == 1">
                                    <label class="btn btn-outline-dark fw-bold rounded-0"
                                        :class="keyFlag == 1 ? 'text-white' : 'text-black'"
                                        for="btnradio2">Objects</label>
                                </div>
                            </div>
                            <hand-sign-component v-if="keyFlag == 0"></hand-sign-component>
                            <object-component v-if="keyFlag == 1"></object-component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Statistics from '../layouts/Statistics.vue'
import BarChart from '../layouts/BarChart.vue'
export default {
    components: {
        Statistics,
        BarChart
    },
    data() {
        return {
            collapsed: false,
            keyFlag: 0,
            gameFlag: 'hangman-game'
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
    },
}
</script>

<style scoped>
.body {
    background-image: url("/images/background.jpg");
    background-size: cover;
    background-repeat: repeat;
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
