<template>
    <template v-if="isLoading">
        <SkeletonLoader></SkeletonLoader>
    </template>
    <div v-else class="row">
        <div v-for="alphabet in alphabets" :key="alphabet" class="col-lg-1 col-md-3 col-sm-4">
            <button @click="viewGraph(alphabet)" style="width: 50px;" :class="active === alphabet ? 'btn-warning' : ' btn-primary'"
                class="btn mb-2 fw-bold">
                {{ alphabet.toUpperCase() }}
            </button>
        </div>
    </div>
    <hr>
    <div class="d-flex justify-content-center">
        <div class="card w-75">
            <div class="card-body">
                <div class="quiz-graph">
                    <h5 class="fw-bold">Quiz</h5>
                    <BarChart v-if="Object.keys(quizChartData).length" :chartData="quizChartData"/>
                </div>
                <hr>
                <div class="quiz-graph">
                    <h5 class="fw-bold">Skill Test</h5>
                    <BarChart v-if="Object.keys(skillTestChartData).length" :chartData="skillTestChartData"/>
                </div>
                <a href="javascript:;" @click="viewStatisticsSummary(this.active.toUpperCase())"
                        class="btn btn-primary rounded-0 mt-5">View All Records</a>
            </div>
        </div>
    </div>
    <div class="statistics-summary">
        <div class="modal fade" id="statisticsModal" tabindex="-1" aria-labelledby="statisticsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statisticsModalLabel">Statistics Summary - Alphabet<span
                                class="fw-bold h3 text-danger"> ({{ selectedAlphabet }})</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-1">
                            <h5 class="fw-bold">Quiz Records</h5>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 15%" scope="col">Item No.</th>
                                        <th style="width: 30%" scope="col">Student Name</th>
                                        <th style="width: 15%" scope="col">Correct</th>
                                        <th style="width: 15%" scope="col">Wrong</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="!Object.keys(quizStatisticsSummary).length">
                                        <tr>
                                            <th class="text-center" scope="row" colspan="5">No records found</th>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <tr v-for="(item, index) in Object.values(quizStatisticsSummary)" :key="index">
                                            <th scope="row">{{ index + 1 }}</th>
                                            <th scope="row">{{ item.full_name }}</th>
                                            <td>{{ item.correct_count }}</td>
                                            <td>{{ item.wrong_count }}</td>
                                            <td>
                                                <button @click="viewAllRecords(item.student_id)"
                                                    class="btn btn-success rounded-0">
                                                    View Student Reports
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="mt-1">
                            <h5 class="fw-bold">Skill Test Records</h5>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 15%" scope="col">Item No.</th>
                                        <th style="width: 30%" scope="col">Student Name</th>
                                        <th style="width: 10%" scope="col">Correct</th>
                                        <th style="width: 10%" scope="col">Wrong</th>
                                        <th style="width: 10%" scope="col">Pending</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="!Object.keys(skillTestStatisticsSummary).length">
                                        <tr>
                                            <th class="text-center" scope="row" colspan="6">No records found</th>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <tr v-for="(item, index) in Object.values(skillTestStatisticsSummary)" :key="index">
                                            <th scope="row">{{ index + 1 }}</th>
                                            <th scope="row">{{ item.full_name }}</th>
                                            <td>{{ item.correct_count }}</td>
                                            <td>{{ item.wrong_count }}</td>
                                            <td>{{ item.pending_count }}</td>
                                            <td>
                                                <button @click="viewAllRecords(item.student_id)"
                                                    class="btn btn-success rounded-0">
                                                    View Student Reports
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
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
        viewStatisticsSummary(alphabet) {
            this.getStatisticsSummary(alphabet)
            this.selectedAlphabet = alphabet
            $('#statisticsModal').modal('show')
        },
        async getStatisticsSummary(alphabet) {
            try {
                const response = await axios.get(`/api/quiz-statistics/summary?game_flag=${this.gameFlag}&textbook_flag=${this.textbookFlag}&alphabet=${alphabet}`)
                this.quizStatisticsSummary = response.data.quiz
                this.skillTestStatisticsSummary = response.data.skill_test
            } catch (error) {
                console.log(error)
            }
        },
        viewAllRecords(student_id) {
            $('#statisticsModal').modal('hide')
            this.$router.push(`/student-quiz-report/${student_id}`)
        },
        viewGraph(alphabet) {
            this.active = alphabet
            const selectedAlphabet = alphabet.toUpperCase()

            // Quiz
            let quizWrong = 0
            let quizCorrect = 0

            if (Object.keys(this.quizStatistics).length && this.quizStatistics.hasOwnProperty(selectedAlphabet)) {
                quizWrong = this.quizStatistics[selectedAlphabet].wrong
                quizCorrect = this.quizStatistics[selectedAlphabet].correct
            }

            this.quizChartData = {
                labels: ['Wrong Answers', 'Correct Answers'],
                datasets: [
                    {
                        data: [quizWrong, quizCorrect],
                        label: 'Quiz',
                        backgroundColor: '#d771d6',
                    }
                ]
            }

            // Skill Test
            let skillTestWrong = 0
            let skillTestCorrect = 0
            let skillTestPending = 0

            if (Object.keys(this.skillTestStatistics).length && this.skillTestStatistics.hasOwnProperty(selectedAlphabet)) {
                skillTestWrong = this.skillTestStatistics[selectedAlphabet].wrong
                skillTestCorrect = this.skillTestStatistics[selectedAlphabet].correct
                skillTestPending = this.skillTestStatistics[selectedAlphabet].pending
            }

            this.skillTestChartData = {
                labels: ['Wrong Answers', 'Correct Answers', 'Pending/Unmarked'],
                datasets: [
                    {
                        data: [skillTestWrong, skillTestCorrect, skillTestPending],
                        label: 'Skill Test',
                        backgroundColor: '#1a9c8b',
                    }
                ]
            }
        }
    },
}
</script>

<style lang="scss" scoped></style>
