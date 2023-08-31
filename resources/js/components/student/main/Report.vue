<template>
    <div class="body">
        <div class="d-flex">
            <student-sidebar-component @collapse="onCollapse" />
            <div :class="collapsed ? 'collapsed-menu' : 'not-collapsed-menu'" class="main-content mx-5 w-100 h-100">
                <div class="d-flex justify-content-center">
                    <div class="card rounded-0 py-5 px-5" style="width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold">Quiz Reports </h4>
                            <img src="/images/kids.png" alt="Kids" width="150">
                        </div>
                        <div class="card-body">
                            <div class="card">
                                <div class="card-body">
                                    <div class="hangman-game mb-3">
                                        <button
                                            class="btn btn-secondary d-block fw-bold rounded-0 w-100 d-flex justify-content-between align-items-center mb-1 fs-6 p-3"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#memoryGame"
                                            aria-expanded="false" aria-controls="memoryGame">
                                            <span class="rounded-0">Memory Game</span>
                                            <i class="fas fa-chevron-circle-down"></i>
                                        </button>
                                        <div class="collapse rounded-0" id="memoryGame">
                                            <div class="card card-body rounded-0">
                                                <template v-if="isLoading">
                                                    <ul class="o-vertical-spacing o-vertical-spacing--l">
                                                        <li class="blog-post o-media">
                                                            <div class="o-media__figure">
                                                                <span class="skeleton-box"
                                                                    style="width:100px;height:80px;"></span>
                                                            </div>
                                                            <div class="o-media__body">
                                                                <div class="o-vertical-spacing">
                                                                    <h3 class="blog-post__headline">
                                                                        <span class="skeleton-box"
                                                                            style="width:55%;"></span>
                                                                    </h3>
                                                                    <p>
                                                                        <span class="skeleton-box"
                                                                            style="width:80%;"></span>
                                                                        <span class="skeleton-box"
                                                                            style="width:90%;"></span>
                                                                        <span class="skeleton-box"
                                                                            style="width:83%;"></span>
                                                                        <span class="skeleton-box"
                                                                            style="width:80%;"></span>
                                                                    </p>
                                                                    <div class="blog-post__meta">
                                                                        <span class="skeleton-box"
                                                                            style="width:70px;"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </template>
                                                <template v-else>
                                                    <h6 class="fw-bold">Perfect Score: <span class="text-danger">{{ perfectScore.memory_game }}</span></h6>
                                                    <table class="table table-bordered table-responsive">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Attempt Number</th>
                                                                <th scope="col">Date/Time</th>
                                                                <th scope="col">Score</th>
                                                                <th scope="col">Percentage</th>
                                                                <th scope="col">Mark</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody v-if="memoryGame.length === 0">
                                                            <tr>
                                                                <td colspan="5" class="text-center fw-bold">No data found
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tbody v-else>
                                                            <tr v-for="item in memoryGame" :key="item.id">
                                                                <td>{{ item.attempt_number }}</td>
                                                                <td>{{ new Date(item.created_at).toLocaleString() }}</td>
                                                                <td>{{ `${item.total_score} / ${perfectScore.memory_game}` }}</td>
                                                                <td>{{ percentage(item.total_score, 'mg') }}%</td>
                                                                <td>{{ item.mark.toUpperCase() }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <h6 class="fw-bold">Highest Score: <span class="fw-bold text-danger">{{ highestScores['memory_game'] ?? 0 }}/{{ perfectScore.memory_game }}</span></h6>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="typing-balloon mb-3">
                                        <button
                                            class="btn btn-secondary d-block fw-bold rounded-0 w-100 d-flex justify-content-between align-items-center mb-1 fs-6 p-3"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#typingBalloon"
                                            aria-expanded="false" aria-controls="typingBalloon">
                                            <span>Typing Balloon</span>
                                            <i class="fas fa-chevron-circle-down"></i>
                                        </button>
                                        <div class="collapse rounded-0" id="typingBalloon">
                                            <div class="card card-body rounded-0">
                                                <template v-if="isLoading">
                                                    <ul class="o-vertical-spacing o-vertical-spacing--l">
                                                        <li class="blog-post o-media">
                                                            <div class="o-media__figure">
                                                                <span class="skeleton-box"
                                                                    style="width:100px;height:80px;"></span>
                                                            </div>
                                                            <div class="o-media__body">
                                                                <div class="o-vertical-spacing">
                                                                    <h3 class="blog-post__headline">
                                                                        <span class="skeleton-box"
                                                                            style="width:55%;"></span>
                                                                    </h3>
                                                                    <p>
                                                                        <span class="skeleton-box"
                                                                            style="width:80%;"></span>
                                                                        <span class="skeleton-box"
                                                                            style="width:90%;"></span>
                                                                        <span class="skeleton-box"
                                                                            style="width:83%;"></span>
                                                                        <span class="skeleton-box"
                                                                            style="width:80%;"></span>
                                                                    </p>
                                                                    <div class="blog-post__meta">
                                                                        <span class="skeleton-box"
                                                                            style="width:70px;"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </template>
                                                <template v-else>
                                                    <h6 class="fw-bold">Perfect Score: <span class="text-danger">{{ perfectScore.typing_balloon }}</span></h6>
                                                    <table class="table table-bordered table-responsive">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Attempt Number</th>
                                                                <th scope="col">Date/Time</th>
                                                                <th scope="col">Score</th>
                                                                <th scope="col">Percentage</th>
                                                                <th scope="col">Mark</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody v-if="typingBalloon.length === 0">
                                                            <tr>
                                                                <td colspan="5" class="text-center fw-bold">No data found
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tbody v-else>
                                                            <tr v-for="item in typingBalloon" :key="item.id">
                                                                <td>{{ item.attempt_number }}</td>
                                                                <td>{{ new Date(item.created_at).toLocaleString() }}</td>
                                                                <td>{{ `${item.total_score} / ${perfectScore.typing_balloon}` }}</td>
                                                                <td>{{ percentage(item.total_score, 'tp') }}%</td>
                                                                <td>{{ item.mark.toUpperCase() }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <h6 class="fw-bold">Highest Score: <span class="fw-bold text-danger">{{ highestScores['typing_balloon'] ?? 0 }}/{{ perfectScore.typing_balloon }}</span></h6>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="memory-game">
                                        <button
                                            class="btn btn-secondary d-block fw-bold rounded-0 w-100 d-flex justify-content-between align-items-center mb-1 fs-6 p-3"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#hangmanGame"
                                            aria-expanded="false" aria-controls="hangmanGame">
                                            <span>Hangman Game</span>
                                            <i class="fas fa-chevron-circle-down"></i>
                                        </button>
                                        <div class="collapse rounded-0" id="hangmanGame">
                                            <div class="card card-body rounded-0">
                                                <template v-if="isLoading">
                                                    <ul class="o-vertical-spacing o-vertical-spacing--l">
                                                        <li class="blog-post o-media">
                                                            <div class="o-media__figure">
                                                                <span class="skeleton-box"
                                                                    style="width:100px;height:80px;"></span>
                                                            </div>
                                                            <div class="o-media__body">
                                                                <div class="o-vertical-spacing">
                                                                    <h3 class="blog-post__headline">
                                                                        <span class="skeleton-box"
                                                                            style="width:55%;"></span>
                                                                    </h3>
                                                                    <p>
                                                                        <span class="skeleton-box"
                                                                            style="width:80%;"></span>
                                                                        <span class="skeleton-box"
                                                                            style="width:90%;"></span>
                                                                        <span class="skeleton-box"
                                                                            style="width:83%;"></span>
                                                                        <span class="skeleton-box"
                                                                            style="width:80%;"></span>
                                                                    </p>
                                                                    <div class="blog-post__meta">
                                                                        <span class="skeleton-box"
                                                                            style="width:70px;"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </template>
                                                <template v-else>
                                                    <h6 class="fw-bold">Perfect Score: <span class="text-danger">{{ perfectScore.hangman_game }}</span></h6>
                                                    <table class="table table-bordered table-responsive">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Attempt Number</th>
                                                                <th scope="col">Date/Time</th>
                                                                <th scope="col">Score</th>
                                                                <th scope="col">Percentage</th>
                                                                <th scope="col">Mark</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody v-if="hangmanGame.length === 0">
                                                            <tr>
                                                                <td colspan="5" class="text-center fw-bold">No data found
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tbody v-else>
                                                            <tr v-for="item in hangmanGame" :key="item.id">
                                                                <td>{{ item.attempt_number }}</td>
                                                                <td>{{ new Date(item.created_at).toLocaleString() }}</td>
                                                                <td>{{ `${item.total_score} / ${perfectScore.hangman_game}` }}</td>
                                                                <td>{{ percentage(item.total_score, 'hg') }}%</td>
                                                                <td>{{ item.mark.toUpperCase() }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <h6 class="fw-bold">Highest Score: <span class="fw-bold text-danger">{{ highestScores['hangman_game'] ?? 0 }}/{{ perfectScore.hangman_game }}</span></h6>
                                                </template>
                                            </div>
                                        </div>
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
export default {
    data() {
        return {
            collapsed: false,
            isProcessing: false,
            isLoading: false,
            data: [],
            highestScores: {}
        };
    },
    created() {
        this.getQuizReports();
    },
    computed: {
        gameId() {
            return this.$route.params.gameId
        },
        cardTitle() {
            if (this.gameId == 1) {
                return 'Hangman Game Quiz Report'
            } else if (this.gameId == 2) {
                return 'Typing Balloon Quiz Report'
            } else if (this.gameId == 3) {
                return 'Memory Game Quiz Report'
            }
        },
        perfectScore() {
            return {
                memory_game: 59,
                hangman_game: 18,
                typing_balloon: 51
            }
        },
        hangmanGame() {
            if (this.data && this.data.length) {
                return this.data.filter((data) => {
                    return data.game_id === 1
                })
            }
            return this.data
        },
        memoryGame() {
            if (this.data && this.data.length) {
                return this.data.filter((data) => {
                    return data.game_id === 3
                })
            }
            return this.data
        },
        typingBalloon() {
            if (this.data && this.data.length) {
                return this.data.filter((data) => {
                    return data.game_id === 2
                })
            }
            return this.data
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
        percentage(score, flag) {
            if (flag === 'hg')
                return Math.round((score / this.perfectScore.hangman_game) * 100);
            else if (flag === 'tp')
                return Math.round((score / this.perfectScore.typing_balloon) * 100);
            else
                return Math.round((score / this.perfectScore.memory_game) * 100);
        },
        async getQuizReports() {
            this.isProcessing = true;
            const data = await axios.get("/api/quiz/reports/get");
            this.data = data.data.reports
            this.highestScores = data.data.highest_scores
            this.isProcessing = false;
        }
    }
}
</script>

<style lang="scss" scoped>
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

.help-ui {
    position: absolute;
    bottom: 20px;
    right: 20px;
}

.skeleton-box {
    display: inline-block;
    height: 1em;
    position: relative;
    overflow: hidden;
    background-color: #DDDBDD;

    &::after {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        transform: translateX(-100%);
        background-image: linear-gradient(90deg,
                rgba(#fff, 0) 0,
                rgba(#fff, 0.2) 20%,
                rgba(#fff, 0.5) 60%,
                rgba(#fff, 0));
        animation: shimmer 5s infinite;
        content: '';
    }

    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }
}

.blog-post {
    &__headline {
        font-size: 1.25em;
        font-weight: bold;
    }

    &__meta {
        font-size: 0.85em;
        color: #6b6b6b;
    }
}

// OBJECTS

.o-media {
    display: flex;

    &__body {
        flex-grow: 1;
        margin-left: 1em;
    }
}

.o-vertical-spacing {
    >*+* {
        margin-top: 0.75em;
    }

    &--l {
        >*+* {
            margin-top: 2em;
        }
    }
}
</style>
