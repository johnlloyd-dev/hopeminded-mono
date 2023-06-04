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
                                <td>{{ item.total_score }}</td>
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
                                    <input ref="fileInput" accept="image/*, .doc, .docx, .pdf" name="file" @change="parseFile" type="file" class="form-control"
                                        id="certificate">
                                    <small class="text-danger" v-if="errors && errors.file">{{ errors.file[0] }}</small>
                                </div>
                                <button v-if="!isProcessing" style="width: 180px" type="submit" class="btn btn-success rounded-0 fw-bold">Upload
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
                                <a :href="item.file_url" download style="margin-right: 20px">{{ item.file}}</a>
                                <button type="button" @click="deleteCertificate(item.id, item.file)" class="btn btn-danger btn-sm rounded-0"><i class="fas fa-trash-alt"></i></button>
                            </li>
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
            gameId: 1,
            studentId: null,
            file: '',
            certificates: [],
            isProcessing: false,
            isLoading: false,
            errors: [],
            certificate: {
                file: null
            }
        }
    }, 
    created() {
        this.studentId = this.$route.params.studentId
        this.getReports()
        this.getCertificates()
    },
    watch: {
        gameId() {
            this.certificates = []
            this.isLoading = true
            this.filterReports()
            this.getCertificates()
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
        filterReports() {
            this.reports = this.data.filter((data) => {
                return data.game_id == this.gameId
            })
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
            const data = {id: id, file: filePath}
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
</style>