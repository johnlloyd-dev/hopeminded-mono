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
                            <div class="row mt-5">
                                <div class="col-4">
                                    <button @click="$router.push({ name: 'quiz-report', params: { gameId: 3 } })"
                                        type="button" class="btn btn-warning fw-bold rounded-0">
                                        Memory Game
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button @click="$router.push({ name: 'quiz-report', params: { gameId: 2 } })"
                                        type="button" class="btn btn-success fw-bold rounded-0">
                                        Typing Balloon
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button @click="$router.push({ name: 'quiz-report', params: { gameId: 1 } })"
                                        type="button" class="btn btn-primary fw-bold rounded-0">
                                        Hangman Game
                                    </button>
                                </div>
                            </div>
                            <div class="certificates mt-5">
                                <div class="card rounded-0">
                                    <div class="card-body">
                                        <h5 class="fw-bold fst-italic">Certificates</h5>
                                        <p>These are certicates given by your teacher once you completed each of the
                                            textbook
                                            lessons and able to pass each of the quizzes.</p>
                                        <div class="accordion rounded-0" id="accordionExample">
                                            <div class="accordion-item rounded-0">
                                                <h2 class="accordion-header" id="headingOne">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                        aria-expanded="false" aria-controls="collapseOne">
                                                        Alphabet-Letters/Memory Game
                                                    </button>
                                                </h2>
                                                <div id="collapseOne" class="accordion-collapse collapse bg-secondary rounded-0"
                                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <span class="text-white" v-if="certificates.memory.length === 0">No
                                                            certificate/s found</span>
                                                        <ul v-else v-for="item in certificates.memory" :key="item.id"
                                                            class="list-group">
                                                            <li class="list-group-item"><a :href="item.file_url" download>{{
                                                                item.file }}</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item rounded-0">
                                                <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                        aria-expanded="false" aria-controls="collapseTwo">
                                                        Vowels-Consonants/Typing Balloon
                                                    </button>
                                                </h2>
                                                <div id="collapseTwo" class="accordion-collapse collapse bg-secondary rounded-0"
                                                    aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <span class="text-white" v-if="certificates.typing.length === 0">No
                                                            certificate/s found</span>
                                                        <ul v-else v-for="item in certificates.typing" :key="item.id"
                                                            class="list-group">
                                                            <li class="list-group-item"><a :href="item.file_url" download>{{
                                                                item.file }}</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item rounded-0">
                                                <h2 class="accordion-header" id="headingThree">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                        aria-expanded="false" aria-controls="collapseThree">
                                                        Alphabet-Words/Hangman Game
                                                    </button>
                                                </h2>
                                                <div id="collapseThree" class="accordion-collapse collapse bg-secondary rounded-0"
                                                    aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <span class="text-white" v-if="certificates.hangman.length === 0">No
                                                            certificate/s found</span>
                                                        <ul v-else v-for="item in certificates.hangman" :key="item.id"
                                                            class="list-group rounded-0">
                                                            <li class="list-group-item">
                                                                <a style="margin-right: 30px;" :href="item.file_url"
                                                                    download>{{ item.file }}</a>
                                                                <button type="button" @click="viewFile(item.file_url)"
                                                                    class="btn btn-primary rounded-0 btn-sm">
                                                                    View Certificate
                                                                    <i class="fas fa-external-link-alt"></i>
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal -->
                            <div class="modal fade" id="fileViewer" data-bs-backdrop="static" tabindex="-1" aria-labelledby="fileViewerLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="fileViewerLabel">View Certificate</h5>
                                            <button @click="activateObject = false" type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center" style="height: 70vh;">
                                            <object v-if="activateObject && documentFileExtensions.includes(fileExtension)" :data="fileUrl" :type="fileType" width="100%" height="100%">
                                            </object>
                                            <img v-if="activateObject && imageFileExtensions.includes(fileExtension)" :src="fileUrl" alt="Certificate Image">
                                        </div>
                                        <div class="modal-footer">
                                            <a :href="fileUrl" download class="btn btn-primary">Download</a>
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
import { Tooltip } from 'bootstrap'
export default {
    data() {
        return {
            isProcessing: false,
            collapsed: false,
            isMemoryGameNotAllowed: false,
            isTypingBalloonNotAllowed: false,
            isHangmanGameNotAllowed: false,
            fileType: null,
            fileUrl: null,
            fileExtension: null,
            imageFileExtensions: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp', 'tiff'],
            documentFileExtensions: ['doc', 'docx', 'txt', 'pdf', 'xls', 'xlsx', 'ppt', 'pptx', 'csv'],
            activateObject: false,
            certificates: {
                hangman: [],
                memory: [],
                typing: []
            }
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
        async getCertificates() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/student-certificates/all`)
                this.certificates.hangman = response.data.filter(data => {
                    return data.game_flag == 'hangman-game'
                })
                this.certificates.memory = response.data.filter(data => {
                    return data.game_flag == 'memory-game'
                })
                this.certificates.typing = response.data.filter(data => {
                    return data.game_flag == 'typing-balloon'
                })
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false
            }
        },
        viewFile(url) {
            this.fileExtension = this.getFileTypeFromURL(url);
            this.fileType = null
            this.fileUrl = url

            if (this.imageFileExtensions.includes(this.fileExtension)) {
                this.fileType = `image/${this.fileExtension}`
            } else if (this.documentFileExtensions.includes(this.fileExtension)) {
                this.fileType = `application/${this.fileExtension}`
            }

            this.activateObject = true

            $('#fileViewer').modal('show');
        },
        getFileTypeFromURL(url) {
            // Create a new URL object with the provided URL
            const urlObj = new URL(`http://${url}`);

            // Get the file extension from the pathname of the URL
            const path = urlObj.pathname;
            const extension = path.substring(path.lastIndexOf('.') + 1);

            // Return the file extension
            return extension;
        }
    },
    mounted() {
        this.getAlLeFlags()
        this.getVoCoFlags()
        this.getAlWoFlags()
        this.getCertificates()
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
    overflow-x: hidden;
}

.card {
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
}</style>