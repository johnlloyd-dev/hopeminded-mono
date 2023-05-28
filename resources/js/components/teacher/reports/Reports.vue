<template>
    <div class="body">
        <div class="d-flex">
            <div class="main-content mx-5 w-100 h-100">
                <div class="my-3">
                    <button @click="navigate()" class="btn-secondary btn rounded-0">
                        Back
                    </button>
                </div>
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" v-model="gameId" :value="1" id="btnradio1" autocomplete="off"
                        :checked="gameId == 1">
                    <label class="btn btn-outline-dark fw-bold rounded-0" :class="gameId == 1 ? 'text-white' : 'text-black'"
                        for="btnradio1">Hangman Game</label>

                    <input type="radio" class="btn-check" v-model="gameId" :value="2" id="btnradio2" autocomplete="off"
                        :checked="gameId == 2">
                    <label class="btn btn-outline-dark fw-bold rounded-0" :class="gameId == 2 ? 'text-white' : 'text-black'"
                        for="btnradio2">Typing Balloon</label>

                    <input type="radio" class="btn-check" v-model="gameId" :value="3" id="btnradio3" autocomplete="off"
                        :checked="gameId == 3">
                    <label class="btn btn-outline-dark fw-bold rounded-0" :class="gameId == 3 ? 'text-white' : 'text-black'"
                        for="btnradio3">Memory Game</label>
                </div>
                <h3 class="fw-bold mt-5 mb-2">{{ gameName }}</h3>
                <div class="d-flex justify-content-center">
                    <table class="table table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">Date/Time</th>
                                <th>Score</th>
                                <th>Mark</th>
                            </tr>
                        </thead>
                        <tbody v-if="reports.length == 0">
                            <tr>
                                <td colspan="4" class="text-center fw-bold">No data found</td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr v-for="item in reports" :key="item.id">
                                <td>{{ new Date(item.created_at).toLocaleString() }}</td>
                                <td>{{ item.total_score }}</td>
                                <td>{{ item.mark }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="reports.length !== 0" class="d-flex justify-content-around">
                    <button class="btn btn-secondary rounded-0">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="btn btn-secondary rounded-0">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            reports: [],
            data: [],
            gameId: 1
        }
    },
    created() {
        this.getReports()
    },
    watch: {
        gameId() {
            this.filterReports()
        }
    },
    computed: {
        gameName() {
            if (this.gameId == 1) {
                return 'Hangman Game'
            } else if (this.gameId == 2) {
                return 'Typing Balloon'
            } else {
                return 'Memory Game'
            }
        }
    },
    methods: {
        async getReports() {
            const studentId = this.$route.params.studentId
            try {
                const data = await axios.get(`/api/quiz-report/student/${studentId}`)
                this.data = data.data
                this.filterReports()
            } catch (error) {
                console.log(error)
            }
        },
        filterReports() {
            this.reports = this.data.filter((data) => {
                return data.game_id == this.gameId
            })
        },
        navigate() {
            this.$router.push('/student-management')
        }
    },
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
}
</style>