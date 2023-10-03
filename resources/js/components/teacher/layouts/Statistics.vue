<template>
    <template v-if="isLoading">
        <SkeletonLoader></SkeletonLoader>
    </template>
    <div v-else class="row">
        <template v-for="alphabet in alphabets" :key="alphabet">
            <div class="col-lg-4 col-sm-6 mb-3">
                <div class="card" style="width: 18rem;">
                    <div class="card-header">
                        <h3 class="card-title fw-bold mb-0 text-center">{{ alphabet.toUpperCase() }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="quiz-records mb-3">
                            <h5 class="fw-bold">Quiz</h5>
                            <p class="card-text mb-0">Wrong Answers: <span class="fw-bold text-danger">{{
                                Object.keys(quizStatistics).length && quizStatistics.hasOwnProperty(alphabet.toUpperCase()) ?
                                quizStatistics[alphabet.toUpperCase()].wrong : 0 }}</span></p>
                            <p class="card-text mb-0">Correct Answers: <span class="fw-bold text-success">{{
                                Object.keys(quizStatistics).length && quizStatistics.hasOwnProperty(alphabet.toUpperCase()) ?
                                quizStatistics[alphabet.toUpperCase()].correct : 0 }}</span></p>
                        </div>
                        <div class="skill-test-records mb-3">
                            <h5 class="fw-bold">Skill Test</h5>
                            <p class="card-text mb-0">Wrong Answers: <span class="fw-bold text-danger">{{
                                Object.keys(skillTestStatistics).length && skillTestStatistics.hasOwnProperty(alphabet.toUpperCase()) ?
                                skillTestStatistics[alphabet.toUpperCase()].wrong : 0 }}</span></p>
                            <p class="card-text mb-0">Correct Answers: <span class="fw-bold text-success">{{
                                Object.keys(skillTestStatistics).length && skillTestStatistics.hasOwnProperty(alphabet.toUpperCase()) ?
                                skillTestStatistics[alphabet.toUpperCase()].correct : 0 }}</span></p>
                            <p class="card-text mb-0">Unmarked/Pending: <span class="fw-bold text-warning">{{
                                Object.keys(skillTestStatistics).length && skillTestStatistics.hasOwnProperty(alphabet.toUpperCase()) ?
                                skillTestStatistics[alphabet.toUpperCase()].pending : 0 }}</span></p>
                        </div>
                        <a href="javascript:;" @click="viewStatisticsSummary(alphabet.toUpperCase())"
                            class="btn btn-primary rounded-0">View All Records</a>
                    </div>
                </div>
            </div>
        </template>
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
                                            <th scope="row">{{ index+1 }}</th>
                                            <th scope="row">{{ item.full_name }}</th>
                                            <td>{{ item.correct_count }}</td>
                                            <td>{{ item.wrong_count }}</td>
                                            <td>
                                                <button @click="viewAllRecords(item.student_id)" class="btn btn-success rounded-0">
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
                                            <th scope="row">{{ index+1 }}</th>
                                            <th scope="row">{{ item.full_name }}</th>
                                            <td>{{ item.correct_count }}</td>
                                            <td>{{ item.wrong_count }}</td>
                                            <td>{{ item.pending_count }}</td>
                                            <td>
                                                <button @click="viewAllRecords(item.student_id)" class="btn btn-success rounded-0">
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
export default {
    name: 'Statistics',
    components: {
        SkeletonLoader
    },
    props: ['gameFlag'],
    data() {
        return {
            quizStatistics: {},
            skillTestStatistics: {},
            quizStatisticsSummary: {},
            skillTestStatisticsSummary: {},
            isLoading: false,
            selectedAlphabet: null,
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
        }
    },
}
</script>

<style lang="scss" scoped></style>
