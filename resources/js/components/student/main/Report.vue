<template>
    <div class="body">
        <div class="d-flex">
            <student-sidebar-component @collapse="onCollapse" />
            <div :class="collapsed ? 'collapsed-menu' : 'not-collapsed-menu'" class="main-content mx-5 w-100 h-100">
                <div class="d-flex justify-content-center">
                    <div class="card rounded-0 py-5 px-5" style="width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Quiz Reports </h4>
                            <img src="/images/kids.png" alt="Kids" width="150">
                        </div>
                        <div class="card-body">
                            <div class="row mt-5">
                                <div class="col-4">
                                    <button @click="$router.push({ name: 'quiz-report', params: { gameId: 3 } })"
                                        type="button" class="btn btn-warning fw-bold">
                                        Memory Game
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button @click="$router.push({ name: 'quiz-report', params: { gameId: 2 } })"
                                        type="button" class="btn btn-success fw-bold">
                                        Typing Balloon
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button @click="$router.push({ name: 'quiz-report', params: { gameId: 1 } })"
                                        type="button" class="btn btn-primary fw-bold">
                                        Hangman Game
                                    </button>
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
            isMemoryGameNotAllowed: false,
            isTypingBalloonNotAllowed: false,
            isHangmanGameNotAllowed: false
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
            this.isMemoryGameNotAllowed = Object.values(attributes).some(value => value === false);
        },
        async getVoCoFlags() {
            const flags = await axios.get(`/api/flags/vowel-consonants`)
            const attributes = JSON.parse(flags.data.attributes)
            this.isTypingBalloonNotAllowed = Object.values(attributes).some(value => value === false);
        },
        async getAlWoFlags() {
            const flags = await axios.get(`/api/flags/alphabet-words`)
            const attributes = JSON.parse(flags.data.attributes)
            this.isHangmanGameNotAllowed = Object.values(attributes).some(value => value.length === 0);
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
}

.card {
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
}
</style>