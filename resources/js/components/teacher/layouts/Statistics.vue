<template>
    <div class="d-flex align-items-center flex-column">
        <div class="card w-75 mb-3">
            <div class="card-body">
                <div class="quiz-graph mb-3">
                    <h5 class="fw-bold">Quiz Statistics</h5>
                    <BarChart v-if="Object.keys(quizChartData).length" :chartData="quizChartData" />
                </div>
                <div class="d-flex flex-column w-50">
                    <button @click="viewStatisticsSummary('quiz', 'wrong')"
                        class="btn btn-success rounded-0 mb-1 fw-bold d-flex justify-content-between align-items-center">View
                        students with wrong answers <i class="fas fa-chevron-circle-right"></i></button>
                    <button @click="viewStatisticsSummary('quiz', 'correct')"
                        class="btn btn-secondary rounded-0 fw-bold d-flex justify-content-between align-items-center">View
                        students with correct answers <i class="fas fa-chevron-circle-right"></i></button>
                </div>
            </div>
        </div>
        <div class="card w-75">
            <div class="card-body">
                <div class="quiz-graph mb-3">
                    <h5 class="fw-bold">Skill Test Statistics</h5>
                    <BarChart v-if="Object.keys(skillTestChartData).length" :chartData="skillTestChartData" />
                </div>
                <div class="d-flex flex-column w-50">
                    <button @click="viewStatisticsSummary('skill_test', 'wrong')"
                        class="btn btn-success rounded-0 mb-1 fw-bold d-flex justify-content-between align-items-center">View
                        students with wrong answers <i class="fas fa-chevron-circle-right"></i></button>
                    <button @click="viewStatisticsSummary('skill_test', 'correct')"
                        class="btn btn-secondary rounded-0 mb-1 fw-bold d-flex justify-content-between align-items-center">View
                        students with correct answers <i class="fas fa-chevron-circle-right"></i></button>
                    <botton @click="viewStatisticsSummary('skill_test', 'pending')"
                        class="btn btn-primary rounded-0 fw-bold d-flex justify-content-between align-items-center">View
                        students with unmarked answers <i class="fas fa-chevron-circle-right"></i></botton>
                </div>
            </div>
        </div>
    </div>
    <div class="statistics-summary">
        <div class="modal fade" id="statisticsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="statisticsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statisticsModalLabel">Statistics</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-1">
                            <h5 class="fw-bold">Quiz Records</h5>
                            <template v-for="(alphabet, index) of alphabets" :key="index">
                                <div class="card rounded-0 mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0 fw-bold">{{ alphabet.toUpperCase() }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <table v-if="statisticsSummary.hasOwnProperty(alphabet)"
                                            class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30%" scope="col">Student Name</th>
                                                    <th style="width: 30%" scope="col">Count</th>
                                                    <th scope="col">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in statisticsSummary[alphabet]" :key="index">
                                                    <th style="width: 30%" scope="row">{{ item.full_name }}</th>
                                                    <th style="width: 30%" scope="row">{{ item.count }}</th>
                                                    <td>
                                                        <button @click="viewAllRecords(item.student_id)"
                                                            class="btn btn-success rounded-0">
                                                            View Student Reports
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <p v-else class="text-center fw-bold mb-0">No records found</p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-0" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import SkeletonLoader from "../layouts/SkeletonLoader.vue"
import BarChart from '../layouts/BarChart.vue'
export default {
    name: 'Statistics',
    components: {
        SkeletonLoader,
        BarChart
    },
    props: ['gameFlag'],
    data() {
        return {
            active: null,
            quizChartData: {},
            skillTestChartData: {},
            quizStatistics: {},
            skillTestStatistics: {},
            quizStatisticsSummary: {},
            skillTestStatisticsSummary: {},
            statisticsSummary: [],
            isLoading: false,
            selectedAlphabet: null,
            vowelAlphabets: ['a', 'e', 'i', 'o', 'u'],
            textbookFlag: 'alphabet-words'
        }
    },
    watch: {
        gameFlag(newGameFlag) {
            if (newGameFlag === 'hangman-game') {
                this.textbookFlag = 'alphabet-words'
            } else if (newGameFlag === 'typing-balloon') {
                this.textbookFlag = 'vowel-consonants'
            } else if (newGameFlag === 'memory-game') {
                this.textbookFlag = 'alphabet-letters'
            }

            this.getStatistics()
        }
    },
    computed: {
        alphabets() {
            return Array.from({ length: 26 }, (_, i) => String.fromCharCode(97 + i));
        }
    },
    mounted() {
        this.getStatistics()
    },
    methods: {
        async getStatistics() {
            try {
                this.isLoading = true
                const response = await axios.get(`/api/quiz-statistics?game_flag=${this.gameFlag}&textbook_flag=${this.textbookFlag}`)
                this.quizStatistics = response.data.quiz
                this.skillTestStatistics = response.data.skill_test
                this.active = 'a'
                this.viewGraph(this.active)
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false
            }
        },
        async viewStatisticsSummary(category, flag) {
            await this.getStatisticsSummary(category, flag)
            // this.selectedAlphabet = alphabet
            $('#statisticsModal').modal('show')
        },
        async getStatisticsSummary(category, flag) {
            try {
                const params = {
                    game_flag: this.gameFlag,
                    textbook_flag: this.textbookFlag,
                    category: category,
                    flag: flag
                }

                const response = await axios.get('/api/quiz-statistics/summary', { params: params })
                this.statisticsSummary = response.data
            } catch (error) {
                console.log(error)
            }
        },
        viewAllRecords(student_id) {
            this.$router.push(`/student-quiz-report/${student_id}`)
            $('#statisticsModal').modal('hide')
        },
        viewGraph(alphabet) {
            this.active = alphabet
            const selectedAlphabet = alphabet.toUpperCase()

            // Quiz
            let quizWrong = 0
            let quizCorrect = 0

            if (Object.keys(this.quizStatistics).length) {
                const quizStatistics = Object.values(this.quizStatistics);

                quizWrong = quizStatistics.reduce((result, mark) => result + mark.wrong, 0);
                quizCorrect = quizStatistics.reduce((result, mark) => result + mark.correct, 0);
            }

            this.quizChartData = {
                labels: ['Wrong Answers', 'Correct Answers'],
                datasets: [
                    {
                        data: [quizWrong, quizCorrect],
                        label: 'Quiz',
                        backgroundColor: ['#198754', '#6c757d'],
                    }
                ]
            }

            // Skill Test
            let skillTestWrong = 0
            let skillTestCorrect = 0
            let skillTestPending = 0

            if (Object.keys(this.skillTestStatistics).length) {
                const skillTestStatistics = Object.values(this.skillTestStatistics);

                skillTestWrong = skillTestStatistics.reduce((result, mark) => result + mark.wrong, 0);
                skillTestCorrect = skillTestStatistics.reduce((result, mark) => result + mark.correct, 0);
                skillTestPending = skillTestStatistics.reduce((result, mark) => result + mark.pending, 0);
            }

            this.skillTestChartData = {
                labels: ['Wrong Answers', 'Correct Answers', 'Pending/Unmarked'],
                datasets: [
                    {
                        data: [skillTestWrong, skillTestCorrect, skillTestPending],
                        label: 'Skill Test',
                        backgroundColor: ['#198754', '#6c757d', '#007bff'],
                    }
                ]
            }
        }
    },
}
</script>

<style lang="scss" scoped>
.all-records-list:hover {
    color: blue;
    cursor: pointer;
}

.table-header-position {
    position: fixed;
    top: 0;
    width: 73vw;
    background-color: skyblue;
    z-index: 999;
}
</style>
