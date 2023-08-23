<template>
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="fw-bold mb-2">{{ gameName }}</h3>
        </div>
        <div class="card-body">
            <div class="skill-test mb-3">
                <div class="mb-3">
                    <button
                        class="btn btn-secondary d-block fw-bold rounded-0 w-100 d-flex justify-content-between align-items-center"
                        type="button" data-bs-toggle="collapse" data-bs-target="#skillTestReport" aria-expanded="false"
                        aria-controls="skillTestReport">
                        <span>Skill Test Report</span>
                        <i class="fas fa-chevron-circle-down"></i>
                    </button>
                    <!-- <div style="height: 60px">
                        <span>
                            Perfect score:
                        </span>
                        <div class="d-flex justify-content-between align-items-center w-100 p-3">
                            <template v-if="isEditPerfectScore">
                                <input type="text" class="form-control w-75" placeholder="Enter perfect score">
                                <button @click="isEditPerfectScore = !isEditPerfectScore"
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-save"></i>
                                </button>
                            </template>
                            <template v-else>
                                <h6 class="mb-0 w-75 text-center">0/10</h6>
                                <button @click="isEditPerfectScore = !isEditPerfectScore"
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </template>
                        </div>
                    </div> -->
                </div>
                <div class="collapse show rounded-0" id="skillTestReport">
                    <div class="card card-body rounded-0">
                        <template v-if="isLoading">
                            <ul class="o-vertical-spacing o-vertical-spacing--l">
                                <li class="blog-post o-media">
                                    <div class="o-media__figure">
                                        <span class="skeleton-box" style="width:100px;height:80px;"></span>
                                    </div>
                                    <div class="o-media__body">
                                        <div class="o-vertical-spacing">
                                            <h3 class="blog-post__headline">
                                                <span class="skeleton-box" style="width:55%;"></span>
                                            </h3>
                                            <p>
                                                <span class="skeleton-box" style="width:80%;"></span>
                                                <span class="skeleton-box" style="width:90%;"></span>
                                                <span class="skeleton-box" style="width:83%;"></span>
                                                <span class="skeleton-box" style="width:80%;"></span>
                                            </p>
                                            <div class="blog-post__meta">
                                                <span class="skeleton-box" style="width:70px;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </template>
                        <table v-else class="table table-bordered">
                            <tbody>
                                <tr v-for="(item) in allAphabets" :key="item">
                                    <td>
                                        <template v-if="!skillTest.hasOwnProperty(item)">
                                            <div>
                                                <p class="text-danger">No submitted skill test for this alphabet yet.</p>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="card rounded-0 border-bottom-0">
                                                <div class="row p-1">
                                                    <div class="col-2 d-flex align-items-center">
                                                        <h6 class="mb-0">Alphabet: <span class="fw-bold text-danger">{{
                                                            item.toUpperCase() }}</span></h6>
                                                    </div>
                                                    <div class="col-2 d-flex align-items-center">
                                                        <h6 class="mb-0">Object: <span class="fw-bold text-danger">{{
                                                            skillTest[item][0].object }}</span></h6>
                                                    </div>
                                                    <div class="col-3 d-flex align-items-center">
                                                        Skill Test Video:
                                                        <button @click="playSkillTestVideo(skillTest[item][0].file_url)"
                                                            class="btn border-0">
                                                            <i class="fas fa-play-circle fa-lg text-danger"></i>
                                                        </button>
                                                    </div>
                                                    <div class="col-5 d-flex">
                                                        Allowed Retake:
                                                        <span class="ms-3">
                                                            <template
                                                                v-if="!showSkillTestRetakeModify.hasOwnProperty(item) || !showSkillTestRetakeModify[item]">
                                                                <span style="border-bottom: 3px solid black"
                                                                    class="h5 fw-bold me-3 text-center text-danger">{{ skillTestRetake[item].allowed_retake }}</span>
                                                                <button @click="showSkillTestRetakeModify[item] = true, allowedSkillTestAttempt[item] = skillTestRetake[item].allowed_retake"
                                                                    class="btn btn-success rounded-0 btn-sm border-0">
                                                                    <i class="fas fa-edit fa-sm"></i>
                                                                </button>
                                                            </template>
                                                            <template v-else>
                                                                <form @submit.prevent="allowRetakeSkillTest(skillTestRetake[item], item)" class="d-flex align-items-center">
                                                                    <div>
                                                                        <input type="number" v-model="allowedSkillTestAttempt[item]"
                                                                            class="form-control form-control-sm rounded-0"
                                                                            min="1">
                                                                    </div>
                                                                    <button :disabled="allowedSkillTestAttempt[item] < 1" type="submit"
                                                                        class="btn btn-primary rounded-0 btn-sm border-0">
                                                                        <i class="fas fa-save fa-sm"></i>
                                                                    </button>
                                                                    <button @click="showSkillTestRetakeModify[item] = false"
                                                                        type="button"
                                                                        class="btn btn-secondary rounded-0 btn-sm border-0">
                                                                        <i class="fas fa-close fa-sm"></i>
                                                                    </button>
                                                                </form>
                                                            </template>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 15%">Attempt No.</th>
                                                        <th style="width: 25%">Date/Time</th>
                                                        <th style="width: 20%">Score</th>
                                                        <th style="width: 20%">Percentage</th>
                                                        <th style="width: 20%">Mark</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="fw-bold" v-for="(data, index) in skillTest[item]"
                                                        :key="data.id">
                                                        <td>{{ index + 1 }}</td>
                                                        <td>{{ new Date(data.created_at).toLocaleString() }}</td>
                                                        <td>
                                                            <span>
                                                                <template v-if="!data.is_display">
                                                                    {{ data.score + '/' + data.perfect_score }}
                                                                    <button
                                                                        @click="data.is_display = !data.is_display, newScore[data.letter] = data.score"
                                                                        class="btn btn-success rounded-0 btn-sm border-0">
                                                                        <i class="fas fa-edit fa-sm"></i>
                                                                    </button>
                                                                </template>
                                                                <template v-else>
                                                                    <form class="d-flex align-items-center"
                                                                        @submit.prevent="updateScore(data, index)">
                                                                        <div>
                                                                            <input type="text"
                                                                                v-model="newScore[data.letter]"
                                                                                class="form-control rounded-0" id="score"
                                                                                aria-describedby="emailHelp">
                                                                        </div>
                                                                        <span class="w-25 text-center">/{{
                                                                            data.perfect_score }}</span>
                                                                        <button type="submit"
                                                                            class="btn btn-primary rounded-0 btn-sm border-0">
                                                                            <i class="fas fa-save fa-sm"></i>
                                                                        </button>
                                                                        <button type="button"
                                                                            @click="data.is_display = !data.is_display"
                                                                            class="btn btn-secondary rounded-0 btn-sm border-0">
                                                                            <i class="fas fa-close fa-sm"></i>
                                                                        </button>
                                                                    </form>
                                                                    <small v-if="data.error_message"
                                                                        class="text-danger">Score must not be greater than
                                                                        the perfect score.</small>
                                                                </template>
                                                            </span>
                                                        </td>
                                                        <td>{{ data.percentage }}% of {{ data.perfect_score }}</td>
                                                        <td>{{ data.mark }}</td>
                                                    </tr>
                                                    <tr class="fw-bold border-0">
                                                        <td colspan="1" class="border-0"></td>
                                                        <td class="text-end border-0">Average:</td>
                                                        <td class="border-0">{{ skillTestAverage[item] }}</td>
                                                        <td class="border-0" colspan="2"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
            <div class="quiz-report">
                <div>
                    <button
                        class="btn btn-secondary d-block fw-bold rounded-0 w-100 d-flex justify-content-between align-items-center"
                        type="button" data-bs-toggle="collapse" data-bs-target="#quizReport" aria-expanded="false"
                        aria-controls="quizReport">
                        Quiz Report <i class="fas fa-chevron-circle-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="quizReport">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold my-3">Perfect Score: <span class="text-danger">{{ reports.length ?
                            reports[0].perfect_score : '(No data found)' }}</span></h6>
                        <button @click="viewWeaknesses()" class="btn btn-success btn-sm rounded-0 text-end">
                            Weaknesses <i class="fas fa-book-reader"></i>
                        </button>
                    </div>
                    <div class="card card-body rounded-0">
                        <table class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th style="width: 15%">Attempt No.</th>
                                    <th style="width: 25%">Date/Time</th>
                                    <th style="width: 20%">Score</th>
                                    <th style="width: 20%">Percentage</th>
                                    <th style="width: 20%">Mark</th>
                                </tr>
                            </thead>
                            <tbody v-if="isReportsLoading">
                                <tr>
                                    <td colspan="6" class="text-center fw-bold">Loading...</td>
                                </tr>
                            </tbody>
                            <tbody v-else-if="!isReportsLoading && reports.length == 0">
                                <tr>
                                    <td colspan="6" class="text-center fw-bold">No data found</td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr class="fw-bold" v-for="item in reports" :key="item.id">
                                    <td>{{ item.attempt_number }}</td>
                                    <td>{{ new Date(item.created_at).toLocaleString() }}</td>
                                    <td>{{ item.total_score }}</td>
                                    <td>{{ item.percentage }}% of {{ item.perfect_score }}</td>
                                    <td>{{ upperCaseFirstLetter(item.mark) }} </td>
                                </tr>
                                <tr class="fw-bold border-0">
                                    <td colspan="1" class="border-0"></td>
                                    <td class="text-end border-0">Average:</td>
                                    <td class="border-0">{{ quizReportAverage[this.$parent.gameId] }}</td>
                                    <td class="border-0" colspan="2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade rounded-0" id="skillTestVideoModal" tabindex="-1"
                aria-labelledby="skillTestVideoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Skill Test Video</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body d-flex justify-content-center">
                            <div class="embed-responsive embed-responsive-21by9">
                                <iframe height="500" width="500" class="embed-responsive-item" :src="skillTestVideoUrl"
                                    allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade rounded-0" id="weaknessModal" tabindex="-1" aria-labelledby="weaknessModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Weaknesses</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <template v-if="weaknessesDataLoading">
                                <ul class="o-vertical-spacing o-vertical-spacing--l">
                                    <li class="blog-post o-media">
                                        <div class="o-media__figure">
                                            <span class="skeleton-box" style="width:100px;height:80px;"></span>
                                        </div>
                                        <div class="o-media__body">
                                            <div class="o-vertical-spacing">
                                                <h3 class="blog-post__headline">
                                                    <span class="skeleton-box" style="width:55%;"></span>
                                                </h3>
                                                <p>
                                                    <span class="skeleton-box" style="width:80%;"></span>
                                                    <span class="skeleton-box" style="width:90%;"></span>
                                                    <span class="skeleton-box" style="width:83%;"></span>
                                                    <span class="skeleton-box" style="width:80%;"></span>
                                                </p>
                                                <div class="blog-post__meta">
                                                    <span class="skeleton-box" style="width:70px;"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </template>
                            <div v-else class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <div class="alert alert-warning" role="alert">
                                        <span class="fw-bold">Note: </span>Hangman game is played by selecting the correct
                                        alphabet symbol based on the object image and text presented. If the selected
                                        alphabet sign did not match to the object name, it will be considered wrong.
                                    </div>
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Quiz Answers/Responses
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show"
                                        aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <template v-if="Object.keys(weaknessesData).length">
                                                <div v-for="(item, index) in Object.values(weaknessesData.data)"
                                                    :key="index" class="card mb-1">
                                                    <div class="card-header">
                                                        No. {{ index + 1 }}
                                                    </div>
                                                    <div class="card-body">
                                                        <template v-if="flag === 'alphabet-words'">
                                                            <table class="table table-bordered table-responsive">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 20%">Object Image</th>
                                                                        <th style="width: 20%">Object Name</th>
                                                                        <th style="width: 20%">Student Answer</th>
                                                                        <th style="width: 20%">Answer Image</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody v-if="!item.length">
                                                                    <tr>
                                                                        <td colspan="4" class="text-center fw-bold">No data
                                                                            found</td>
                                                                    </tr>
                                                                </tbody>
                                                                <tbody v-else>
                                                                    <tr :class="item2.attributes.mark === 'wrong' ? 'table-danger' : 'table-success'"
                                                                        class="fw-bold" v-for="item2 in item" :key="item2.id">
                                                                        <td>
                                                                            <img width="100"
                                                                                :src="'/' + item2.attributes.object_image">
                                                                        </td>
                                                                        <td>{{ item2.attributes.object_text }}</td>
                                                                        <td>{{ item2.attributes.answer }}</td>
                                                                        <td>
                                                                            <img width="100"
                                                                                :src="'/' + item2.attributes.answer_image">
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </template>
                                                        <template v-else-if="flag === 'alphabet-letters'">
                                                            <table class="table table-bordered table-responsive">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 20%">Alphabet</th>
                                                                        <th style="width: 20%">Object Image</th>
                                                                        <th style="width: 20%">Object Name</th>
                                                                        <th style="width: 20%">Student Answer</th>
                                                                        <th style="width: 20%">Answer Image</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody v-if="!item.length">
                                                                    <tr>
                                                                        <td colspan="4" class="text-center fw-bold">No data
                                                                            found</td>
                                                                    </tr>
                                                                </tbody>
                                                                <tbody v-else>
                                                                    <tr :class="item2.attributes.mark === 'wrong' ? 'table-danger' : 'table-success'"
                                                                        class="fw-bold" v-for="item2 in item" :key="item2.id">
                                                                        <td>{{ item2.attributes.alphabet }}</td>
                                                                        <td>
                                                                            <img width="100"
                                                                                :src="'/' + item2.attributes.object_image">
                                                                        </td>
                                                                        <td>{{ item2.attributes.object }}</td>
                                                                        <td>{{ item2.attributes.answer }}</td>
                                                                        <td>
                                                                            <img width="100"
                                                                                :src="item2.attributes.answer_image">
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                            <div v-else>
                                                <p class="text-center">No record to display.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseThree" aria-expanded="false"
                                            aria-controls="collapseThree">
                                            Answer Keys
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse"
                                        aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <template v-if="Object.keys(weaknessesData).length">
                                                <table class="table table-bordered table-responsive">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 20%">Object Image</th>
                                                            <th style="width: 20%">Object Name</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody v-if="!weaknessesData.answer_key.length">
                                                        <tr>
                                                            <td colspan="2" class="text-center fw-bold">No data found</td>
                                                        </tr>
                                                    </tbody>
                                                    <tbody v-else>
                                                        <tr class="fw-bold"
                                                            v-for="(item, index) in weaknessesData.answer_key" :key="index">
                                                            <td>
                                                                <img width="100" :src="'/' + item.image">
                                                            </td>
                                                            <td>{{ item.word.toUpperCase() }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </template>
                                            <template v-else>
                                                <div>
                                                    <p class="text-center">No record to display</p>
                                                </div>
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
</template>

<script>
import swal from 'sweetalert2'
export default {
    props: ['gameName', 'studentId', 'flag', 'reports', 'quizReportAverage', 'quizReportRetake'],
    data() {
        return {
            indexes: {
                skillTest: 'a'
            },
            setScore: {
                score: 0,
                skill_test_id: null
            },
            average: 0,
            isEditScore: false,
            isEditPerfectScore: false,
            isLoading: false,
            weaknessesDataLoading: false,
            skillTestVideoUrl: null,
            weaknessesData: {},
            skillTest: {},
            newScore: {},
            allowedSkillTestAttempt: {},
            allowedQuizAttempt: {},
            skillTestAverage: [],
            skillTestRetake: [],
            showSkillTestRetakeModify: {},
            showQuizRetakeModify: {}
        }
    },
    created() {
        this.getSkillTest()
    },
    watch: {
        async flag() {
            this.skillTest = {}
            this.isLoading = true
            await this.getSkillTest()
        }
    },
    computed: {
        allAphabets() {
            return Array.from({ length: 26 }, (_, i) => String.fromCharCode(97 + i));
        },
        isReportsLoading() {
            return this.$parent.isLoading
        }
    },
    methods: {
        async getSkillTest() {
            this.isLoading = true
            try {
                const data = await axios.get(`/api/skill-test/fetch/${this.studentId}/${this.flag}`)
                const mergedObject = data.data.data.reduce((result, item) => {
                    const { letter, ...rest } = item;

                    if (result[letter]) {
                        result[letter].push({ ...rest, letter: item.letter, is_display: false, error_message: false });
                    } else {
                        result[letter] = [{ ...rest, letter: item.letter, is_display: false, error_message: false }];
                    }

                    return result;
                }, {});

                this.skillTest = mergedObject
                if (Object.keys(this.skillTest).length) {
                    this.skillTestAverage = data.data.average ?? 0
                    this.skillTestRetake = data.data.retake ?? []
                }

                this.indexes.skillTest = Object.keys(mergedObject)[0]
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false
            }
        },
        async updateScore(data, index) {
            this.setScore.score = this.newScore[data.letter]
            this.setScore.skill_test_id = data.id

            if (this.setScore.score > data.perfect_score) {
                this.skillTest[data.letter][index].error_message = true
                return
            }

            try {
                const response = await axios.post(`/api/skill-test-score/modify`, this.setScore)
                this.getSkillTest()
                swal.fire('Success', response.data.message, 'success')
                this.skillTest[data.letter][index].error_message = false
            } catch (error) {
                console.log(error)
            }
        },
        upperCaseFirstLetter(text) {
            return text.charAt(0).toUpperCase() + text.slice(1)
        },
        playSkillTestVideo(video_url) {
            this.skillTestVideoUrl = video_url
            $('#skillTestVideoModal').modal('show')
        },
        async viewWeaknesses() {
            try {
                $('#weaknessModal').modal('show')
                let gameFlag = null;
                switch (this.flag) {
                    case 'alphabet-words':
                        gameFlag = 'hangman-game'
                        break;
                    case 'alphabet-letters':
                        gameFlag = 'memory-game'
                        break;
                    case 'vowel-consonants':
                        gameFlag = 'typing-balloon'
                        break;
                }

                this.weaknessesDataLoading = true
                const response = await axios.get(`/api/get-mistakes/${this.studentId}?game_flag=${gameFlag}`)
                if (response.status == 200 && response.data.data.length) {
                    const groupedObjects = response.data.data.reduce((groups, obj) => {
                        const key = obj.quiz_report_id;

                        if (!groups[key]) {
                            groups[key] = [];
                        }

                        groups[key].push(obj);

                        return groups;
                    }, {});
                    this.weaknessesData.data = groupedObjects
                    this.weaknessesData.answer_key = response.data.answer_key
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.weaknessesDataLoading = false
            }
        },
        async allowRetakeSkillTest(data, item) {
            try {
                const response = await axios.put(`/api/retake/skill-test/allow/${data.id}`, { allowed_retake: this.allowedSkillTestAttempt[item] })
                swal.fire('Success', response.data.message, 'success')
                this.getSkillTest()
                this.showSkillTestRetakeModify[item] = false
            } catch (error) {
                console.log(error)
            }
        },
        // async allowRetakeQuiz(data, item) {
        //     try {
        //         const response = await axios.put(`/api/retake/quiz/allow/${data.id}`, { allowed_retake: this.allowedSkillTestAttempt[item] })
        //         swal.fire('Success', response.data.message, 'success')
        //         this.$parent.getReports()
        //         this.showSkillTestRetakeModify[item] = false
        //     } catch (error) {
        //         console.log(error)
        //     }
        // }
    }
}
</script>

<style lang="scss" scoped>
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
