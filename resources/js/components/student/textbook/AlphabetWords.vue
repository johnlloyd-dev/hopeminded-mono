<template>
    <div class="body position-relative">
        <button type="button" @click="navigate" class="btn btn-secondary rounded-0 position-absolute top-0 start-0">
            Back
        </button>
        <div class="container mt-5">
            <div class="row w-100">
                <div class="col-2 border-end border-secondary py-3">
                    <div class="scrollbar position-relative" id="scrollbar1">
                        <template v-if="!isLoading">
                            <div v-if="data.length" class="row">
                                <div class="col-12 mb-3" v-for="(item, index) in data" :key="index">
                                    <button @click="alphabetHandler(index, item)" style="width: 100px" type="button"
                                        class="btn rounded-0 fw-bold me-3"
                                        :class="indexes.letter === index ? 'btn-warning btn-lg' : 'btn-success'">
                                        {{ Object.keys(item)[0].toUpperCase() }}
                                    </button>
                                    <!-- <span v-if="item.isDone"><i class="fas fa-check fa-xl"></i></span> -->
                                </div>
                                <hr class="w-75">
                                <button :disabled="Object.keys(skillTest).length < 5"
                                    @click="$router.push('/quiz-hangman-game')"
                                    class="btn btn-danger btn-lg rounded-0 w-75 fw-bold">Take Quiz</button>
                            </div>
                            <div v-else>
                                <h6 class="position-absolute start-50 top-50 translate-middle fw-bold">No records found</h6>
                            </div>
                        </template>
                        <div v-else>
                            <h6 class="position-absolute start-50 top-50 translate-middle fw-bold">Loading...</h6>
                        </div>
                    </div>
                </div>
                <div class="col-10 position-relative">
                    <template v-if="!isLoading">
                        <div v-if="data[indexes.letter]">
                            <div class="text-center">
                                <div class="row">
                                    <div v-for="(item, index) in newData[0]" :key="item.image"
                                        class="col-lg-4 col-md-6 text-center mb-4">
                                        <div class="position-relative badge-overlay">
                                            <img width="250" height="150" class="rounded box-shadow" :src="item.image"
                                                alt="" />
                                            <!-- <span v-if="item.isDone"
                                                class="img-badge">
                                                <i class="fas fa-check-circle fa-2xl text-black"></i>
                                            </span> -->
                                        </div>
                                        <div class="d-flex flex-column align-items-center">
                                            <div style="width: 75%" v-if="!hasSubmitted(item.object).length" class="alert alert-danger rounded-0 mb-0 mt-3 fw-bold text-center" role="alert">No skill test submitted.</div>
                                            <div style="width: 75%" v-else class="alert alert-success mb-0 fw-bold text-center rounded-0 mt-3" role="alert">Uploaded Skill Test: {{ hasSubmitted(item.object).length }}</div>
                                            <div style="width: 75%" class="d-grid gap-2">
                                                <button type="button" @click="setVideo(index)"
                                                    class="btn btn-primary btn-lg rounded-0 fw-bold mt-3">
                                                    Play <i class="fas fa-play-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div data-bs-toggle="modal" data-bs-target="#skillTestModal"
                                        class="col-lg-4 col-md-6 text-center mb-4 position-relative">
                                        <button type="button"
                                            class="btn btn btn-success position-absolute start-50 top-50 translate-middle rounded-0">
                                            View Submitted Skill Test <i class="fas fa-external-link-alt"></i>
                                        </button>
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
        <!-- Modal -->
        <div class="modal fade" id="alphabetsModal" data-bs-backdrop="static" tabindex="-1"
            aria-labelledby="alphabetsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="alphabetsModalLabel">Hand Sign</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center flex-column">
                            <video id="myVideo" class="video-width" autoplay muted controls loop>
                                <source type="video/mp4" />
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-0" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button @click="active = true" type="button" class="btn btn-primary rounded-0"
                            data-bs-target="#recordVideoModal" data-bs-toggle="modal" data-bs-dismiss="modal">
                            Record a Video
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal 2 -->
        <div class="modal fade" id="recordVideoModal" aria-hidden="true" data-bs-backdrop="static"
            aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalToggleLabel2">Modal 2</h5>
                        <button @click="active = false" type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <video-recorder :active="active"></video-recorder>
                    </div>
                    <div class="modal-footer">
                        <button @click="active = false" class="btn btn-primary rounded-0" data-bs-target="#alphabetsModal"
                            data-bs-toggle="modal" data-bs-dismiss="modal">
                            Back
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal 3 -->
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
                        <div class="container mt-5">
                            <div class="row w-100">
                                <div class="col-2 border-end border-secondary py-3">
                                    <div class="scrollbar position-relative" id="scrollbar1">
                                        <template v-if="!isProcessing">
                                            <div v-if="Object.keys(skillTest).length" class="row">
                                                <div class="col-12 mb-3">
                                                    <button style="width: 100px" type="button"
                                                        class="btn rounded-0 fw-bold me-3 btn-warning btn-lg">
                                                        {{ indexes.skillTest.toUpperCase() }}
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
                                    <template v-if="!isProcessing">
                                        <div v-if="selectedSkillTest && selectedSkillTest.length">
                                            <div class="text-center">
                                                <div class="row">
                                                    <div v-for="(item, index) in selectedSkillTest" :key="index"
                                                        class="col-lg-4 col-md-6 text-center mb-4">
                                                        <div class="position-relative badge-overlay">
                                                            <h5 class="text-center fw-bold">{{ item.object }}</h5>
                                                            <video class="st-width" ref="previewElement"
                                                                :src="item.file_url" controls></video>
                                                        </div>
                                                        <h5 class="text-center fw-bold text-danger">Status: {{
                                                            item.status.toUpperCase() }}</h5>
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
        <!-- </div> -->
    </div>
</template>

<script>
import swal from 'sweetalert2'
import VideoRecorder from "../layouts/VideoRecorder.vue";
import { mapActions, mapGetters } from 'vuex'
export default {
    components: {
        VideoRecorder,
    },
    data() {
        return {
            active: false,
            items: [],
            skillTest: [],
            images: [],
            isProcessing: false,
            pageFlag: 0,
            letter: "",
            video: null,
            indexes: {
                letter: 0,
                skillTest: 'a'
            },
            selected: {
                letter: null,
                object: null
            },
            flag: 'alphabet-words'
        }
    },
    created() {
        this.getFlags()
        this.getSkillTest()
    },
    computed: {
        ...mapGetters({
            data: 'alphabetWords',
            isLoading: "isLoading"
        }),
        newData() {
            return Object.values(this.data[this.indexes.letter])
        },
        selectedSkillTest() {
            return this.skillTest[this.indexes.skillTest] ?? []
        }
    },
    methods: {
        ...mapActions({
            getFlags: 'setAlphabetWords'
        }),
        navigate() {
            this.$router.push('/student-textbook')
        },
        hasSubmitted(object) {
            return this.selectedSkillTest.filter((data) => {
                return data.object === object
            })
        },
        setVideo(index) {
            this.video = this.newData[0][index].video;
            var video = document.getElementById("myVideo");
            video.setAttribute("src", this.video); // set the new video src
            video.load(); // load the new video
            video.play(); // play the new video
            this.selected.letter = Object.keys(this.data[this.indexes.letter])[0]
            this.selected.object = this.newData[0][index].object
            $("#alphabetsModal").modal("show");
            // if (!this.newData[0][index].isDone) {
            //     this.updateFlag()
            // }
        },
        alphabetHandler(index, item) {
            this.indexes.letter = index;
            this.indexes.skillTest = Object.keys(item)[0]
        },
        letterHandler(letter) {
            this.indexes.skillTest = letter
        },
        async getSkillTest() {
            this.isProcessing = true
            try {
                const data = await axios.get(`/api/skill-test/fetch/${null}/${this.flag}?flag=student`)
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
            } catch (error) {
                console.log(error)
            } finally {
                this.isProcessing = false
            }
        },
        setUploadSuccess() {
            $("#alphabetsModal").modal("hide");
            $("#recordVideoModal").modal("hide");
            swal.fire('Success', 'Skill test video has been uploaded successfully.', 'success')
            this.getSkillTest()
        },
        async updateFlag() {
            await axios.put(`/api/update-flag/alphabet-words`, this.selected)
                .then(response => {
                    this.getFlags()
                })
                .catch(error => {
                    console.log(error);
                })
                .finally(() => {
                    this.isProcessing = false
                });
        },
    }
}
</script>

<style lang="scss" scoped>
.body {
    background-image: url("/images/background.jpg");
    background-size: cover;
    background-repeat: repeat;
    background-attachment: fixed;
    color: #333;
    padding: 0;
    margin: 0;
    overflow: hidden;
    height: 100vh;
}

.image-button {
    transition: transform .2s ease-in-out;
}

.image-button:hover {
    border: 1px solid gray;
    transform: scale(1.1);
}

.title-header {
    font-family: 'Bangers', cursive;
    font-size: 50px;
}

.box-shadow {
    box-shadow: 10px 10px 5px gray;
}


.video-width {
    width: 900px;
}

@media screen and (max-width: 994px) {
    .body {
        overflow-y: scroll;
    }
}

@media screen and (max-width: 1200px) {
    .video-width {
        width: 700px;
    }
}

@media screen and (max-width: 991.95px) {
    .video-width {
        width: 450px;
    }
}

.badge-overlay .img-badge {
    position: absolute;
    top: 0;
    right: 5px;
}

.st-width {
    width: 320px;
}</style>
