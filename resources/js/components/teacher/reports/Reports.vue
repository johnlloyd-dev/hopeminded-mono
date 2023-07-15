<template>
    <div class="body position-relative">
        <div class="d-flex">
            <div class="main-content mx-5 w-100 h-100">
                <div class="my-3">
                    <button @click="navigate()" class="btn-secondary btn rounded-0">
                        Back
                    </button>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" v-model="gameId" :value="1" id="btnradio1" autocomplete="off"
                            :checked="gameId == 1">
                        <label class="btn btn-outline-dark fw-bold rounded-0"
                            :class="gameId == 1 ? 'text-white' : 'text-black'" for="btnradio1">Hangman Game</label>

                        <input type="radio" class="btn-check" v-model="gameId" :value="2" id="btnradio2" autocomplete="off"
                            :checked="gameId == 2">
                        <label class="btn btn-outline-dark fw-bold rounded-0"
                            :class="gameId == 2 ? 'text-white' : 'text-black'" for="btnradio2">Typing Balloon</label>

                        <input type="radio" class="btn-check" v-model="gameId" :value="3" id="btnradio3" autocomplete="off"
                            :checked="gameId == 3">
                        <label class="btn btn-outline-dark fw-bold rounded-0"
                            :class="gameId == 3 ? 'text-white' : 'text-black'" for="btnradio3">Memory Game</label>
                    </div>
                    <div>
                        <button data-bs-toggle="modal" data-bs-target="#skillTestModal"
                            class="btn btn-secondary rounded-0 fw-bold">Check Skill Test</button>
                    </div>
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
                        <tbody v-if="isLoading">
                            <tr>
                                <td colspan="3" class="text-center fw-bold">Loading...</td>
                            </tr>
                        </tbody>
                        <tbody v-else-if="!isLoading && reports.length == 0">
                            <tr>
                                <td colspan="3" class="text-center fw-bold">No data found</td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr v-for="item in reports" :key="item.id">
                                <td>{{ new Date(item.created_at).toLocaleString() }}</td>
                                <td>{{ percentage(item.total_score) }}%</td>
                                <td>{{ item.mark }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="reports.length !== 0" class="d-flex justify-content-around">
                    <button :disabled="reports.length <= 10" class="btn btn-secondary rounded-0">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button :disabled="reports.length <= 10" class="btn btn-secondary rounded-0">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="certificate my-3">
                    <div class="card">
                        <img :src="certificate.file" alt="">
                        <div class="card-body">
                            <h3 class="mb-3 fw-bold">Upload Certificates</h3>
                            <form @submit.prevent="uploadCertificate" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <input ref="fileInput" accept="image/*, .doc, .docx, .pdf" name="file"
                                        @change="parseFile" type="file" class="form-control" id="certificate">
                                    <small class="text-danger" v-if="errors && errors.file">{{ errors.file[0] }}</small>
                                </div>
                                <button v-if="!isProcessing" style="width: 180px" type="submit"
                                    class="btn btn-success rounded-0 fw-bold">Upload
                                    Certificate</button>
                                <button v-else disabled style="font-weight: bold; width: 180px;" type="button"
                                    class="btn btn-success rounded-0 pb-0">
                                    <Loading />
                                </button>
                            </form>
                            <hr>
                            <div class="certificates mt-3">
                                <h5 class="fw-bold">Files</h5>
                            </div>
                            <p v-if="isLoading">Loading...</p>
                            <p v-if="!isLoading && certificates.length === 0">No certificates added</p>
                            <li v-else v-for="item in certificates" :key="item.id" class="list-group-item">
                                <a :href="item.file_url" download style="margin-right: 20px">{{ item.file }}</a>
                                <button type="button" @click="deleteCertificate(item.id, item.file)"
                                    class="btn btn-danger btn-sm rounded-0"><i class="fas fa-trash-alt"></i></button>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="skillTestModal" aria-hidden="true" data-bs-backdrop="static"
                aria-labelledby="skillTestModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-fullscreen">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="skillTestModal">Skill Test</h5>
                            <button @click="active = false" type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex justify-content-between">
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" v-model="flag" :value="'alphabet-letters'"
                                        id="btnradio4" autocomplete="off" :checked="flag == 'alphabet-letters'">
                                    <label class="btn btn-outline-dark fw-bold rounded-0"
                                        :class="flag == 'alphabet-letters' ? 'text-white' : 'text-black'"
                                        for="btnradio4">Alphabets/Letters</label>

                                    <input type="radio" class="btn-check" v-model="flag" :value="'vowel-consonants'"
                                        id="btnradio5" autocomplete="off" :checked="flag == 'vowel-consonants'">
                                    <label class="btn btn-outline-dark fw-bold rounded-0"
                                        :class="flag == 'vowel-consonants' ? 'text-white' : 'text-black'"
                                        for="btnradio5">Vowels/Consonants</label>

                                    <input type="radio" class="btn-check" v-model="flag" :value="'alphabet-words'"
                                        id="btnradio6" autocomplete="off" :checked="flag == 'alphabet-words'">
                                    <label class="btn btn-outline-dark fw-bold rounded-0"
                                        :class="flag == 'alphabet-words' ? 'text-white' : 'text-black'"
                                        for="btnradio6">Alphabets/Words</label>
                                </div>
                            </div>
                            <div class="container mt-5">
                                <div class="row w-100">
                                    <div class="col-2 border-end border-secondary py-3">
                                        <div class="scrollbar position-relative" id="scrollbar1">
                                            <template v-if="!isLoading">
                                                <div v-if="Object.keys(skillTest).length" class="row">
                                                    <div class="col-12 mb-3" v-for="(item, index) in Object.keys(skillTest)"
                                                        :key="index">
                                                        <button @click="alphabetHandler(item)" style="width: 100px"
                                                            type="button" class="btn rounded-0 fw-bold me-3"
                                                            :class="indexes.skillTest === item ? 'btn-warning btn-lg' : 'btn-success'">
                                                            {{ item.toUpperCase() }}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div v-else>
                                                    <h6 class="position-absolute start-50 top-50 translate-middle fw-bold">
                                                        No records found</h6>
                                                </div>
                                            </template>
                                            <div v-else>
                                                <h6 class="position-absolute start-50 top-50 translate-middle fw-bold">
                                                    Loading...</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-10 position-relative">
                                        <template v-if="!isLoading">
                                            <div v-if="selectedSkillTest && selectedSkillTest.length">
                                                <div class="text-center">
                                                    <div class="row">
                                                        <div v-for="(item, index) in selectedSkillTest" :key="index"
                                                            class="col-lg-4 col-md-6 text-center mb-4">
                                                            <div class="position-relative badge-overlay">
                                                                <h5 class="text-center fw-bold">{{ item.object }}</h5>
                                                                <video class="video-width" ref="previewElement"
                                                                    :src="item.file_url" controls></video>
                                                            </div>
                                                            <h5 class="text-center fw-bold text-danger">Status: {{ item.status.toUpperCase() }}</h5>
                                                            <div class="mt-3">
                                                                <div class="d-flex justify-content-around px-0">
                                                                    <button type="button" @click="updateSkillTest('correct', item.id)"
                                                                        class="btn btn-success btn-lg rounded-0 fw-bold">
                                                                        Correct <i class="fas fa-check"></i>
                                                                    </button>
                                                                    <button type="button" @click="updateSkillTest('wrong', item.id)"
                                                                        class="btn btn-danger btn-lg rounded-0 fw-bold">
                                                                        Wrong <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="position-absolute start-50 top-50 translate-middle" v-else>
                                                <h1 class="fw-bold">No records found</h1>
                                            </div>
                                        </template>
                                        <div class="position-absolute start-50 top-50 translate-middle" v-else>
                                            <h1 class="fw-bold">Loading...</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button @click="active = false" class="btn btn-primary rounded-0" data-bs-dismiss="modal">
                                Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import swal from 'sweetalert2'
import Loading from '../../loading/Loading.vue'
export default {
    components: {
        Loading
    },
    data() {
        return {
            reports: [],
            data: [],
            skillTest: [],
            gameId: 1,
            studentId: null,
            file: '',
            certificates: [],
            isProcessing: false,
            isLoading: false,
            errors: [],
            flag: 'alphabet-letters',
            formData: {
                statusMark: 'correct'
            },
            indexes: {
                skillTest: 'a'
            },
            certificate: {
                file: null
            }
        }
    },
    created() {
        this.studentId = this.$route.params.studentId
        this.getReports()
        this.getCertificates()
        this.getSkillTest()
    },
    beforeUnmount() {
        $('#skillTestModal').modal('hide')
    },
    watch: {
        gameId() {
            this.certificates = []
            this.isLoading = true
            this.filterReports()
            this.getCertificates()
        },
        flag() {
            this.skillTest = []
            this.isLoading = true
            this.getSkillTest()
        }
    },
    computed: {
        gameName() {
            if (this.gameId == 1) {
                return 'Hangman Game/Alphabets-Words'
            } else if (this.gameId == 2) {
                return 'Typing Balloon/Vowels-Consonants'
            } else {
                return 'Memory Game/Alphabets-Letters'
            }
        },
        selectedSkillTest() {
            return this.skillTest[this.indexes.skillTest] ?? []
        }
    },
    methods: {
        async getReports() {
            this.isLoading = true
            try {
                const data = await axios.get(`/api/quiz-report/student/${this.studentId}`)
                this.data = data.data
                this.filterReports()
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false
            }
        },
        async getCertificates() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/certificates/student/${this.studentId}/game-id/${this.gameId}`)
                this.certificates = response.data
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false
            }
        },
        async getSkillTest() {
            this.isLoading = true
            try {
                const data = await axios.get(`/api/skill-test/fetch/${this.studentId}/${this.flag}`)
                const mergedObject = data.data.reduce((result, item) => {
                    const { letter, ...rest } = item;

                    if (result[letter]) {
                        result[letter].push(rest);
                    } else {
                        result[letter] = [rest];
                    }

                    return result;
                }, {});

                this.skillTest = mergedObject
                this.indexes.skillTest = Object.keys(mergedObject)[0]
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false
            }
        },
        percentage(score) {
            if (this.gameId == 1) {
                return Math.round((score / 18) * 100);
            } else if (this.gameId == 2) {
                return Math.round((score / 51) * 100);
            } else if (this.gameId == 3) {
                return Math.round((score / 59) * 100);
            }
        },
        filterReports() {
            this.reports = this.data.filter((data) => {
                return data.game_id == this.gameId
            })
        },
        alphabetHandler(letter) {
            this.indexes.skillTest = letter
        },
        navigate() {
            this.$router.push('/student-management')
        },
        parseFile(event) {
            this.file = event.target.files[0];
        },
        async uploadCertificate() {
            try {
                this.isProcessing = true
                this.errors = []
                let formData = new FormData();
                formData.append("gameId", this.gameId);
                formData.append("studentId", this.studentId);
                formData.append("file", this.file);
                const response = await axios.post('/api/certificate/upload', formData);
                if (response.status) {
                    this.file = null
                    this.$refs.fileInput.value = '';
                    swal.fire('Success', response.data.message, 'success')
                    this.getCertificates()
                }
            } catch (error) {
                this.errors = error.response.data.errors;
                // Handle the error, e.g., show an error message
            } finally {
                this.isProcessing = false
            }
        },
        async deleteCertificate(id, filePath) {
            const data = { id: id, file: filePath }
            try {
                this.isProcessing = true
                const response = await axios.post('/api/certificate/delete', data)
                if (response.status) {
                    swal.fire('Success', response.data.message, 'success')
                    this.getCertificates()
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.isProcessing = false
            }
        },
        async updateSkillTest(status, id) {
            try {
                const response = await axios.put(`/api/skill-test/update/${id}?status=${status}`)
                if(response.status === 200) {
                    swal.fire('Sucess', response.data.message, 'success')
                    this.getSkillTest();
                }
            } catch (error) {
                console.log(error)
            }
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
    overflow-x: hidden;
}

.video-width {
    width: 320px;
}</style>
