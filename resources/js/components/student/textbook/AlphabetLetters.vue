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
                                        <button @click="alphabetHandler(index, item.letter)" style="width: 100px" type="button"
                                            class="btn rounded-0 fw-bold me-3"
                                            :class="indexes.alphabet === index ? 'btn-warning btn-lg' : 'btn-success'">
                                            {{ item.letter.toUpperCase() }}
                                        </button>
                                        <span v-if="item.isDone"><i class="fas fa-check fa-xl"></i></span>
                                    </div>
                                    <hr class="w-75">
                                    <button :disabled="Object.keys(skillTest).length < 5" @click="$router.push('/quiz-memory-game')" class="btn btn-danger btn-lg rounded-0 w-75 fw-bold">Take Quiz</button>
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
                        <div v-if="selectedAlphabet">
                            <div class="text-center">
                                <img style="width: 60%" class="rounded box-shadow" :src="selectedAlphabet.image_url" alt="" />
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <div style="width: 60%" class="d-grid gap-2">
                                    <button type="button" @click="setVideo()"
                                        class="btn btn-primary btn-lg rounded-0 fw-bold mt-3">
                                        Play <i class="fas fa-play-circle"></i>
                                    </button>
                                    <button data-bs-toggle="modal" data-bs-target="#skillTestModal" type="button"
                                        class="btn btn-success btn-lg rounded-0 fw-bold mt-3">
                                        View Submitted Skill Test <i class="fas fa-external-link-alt"></i>
                                    </button>
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
                        <button @click="active = true" type="button" class="btn btn-primary rounded-0" data-bs-target="#recordVideoModal"
                            data-bs-toggle="modal" data-bs-dismiss="modal">
                            Record a Video
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal 2 -->
        <div class="modal fade" id="recordVideoModal" aria-hidden="true" data-bs-backdrop="static" aria-labelledby="exampleModalToggleLabel2"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalToggleLabel2">Skill Test</h5>
                        <button @click="active = false" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <video-recorder :active="active"></video-recorder>
                    </div>
                    <div class="modal-footer">
                        <button @click="active = false" class="btn btn-primary rounded-0" data-bs-target="#alphabetsModal" data-bs-toggle="modal"
                            data-bs-dismiss="modal">
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
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container mt-5">
                                <div class="row w-100">
                                    <div class="col-2 border-end border-secondary py-3">
                                        <div class="scrollbar position-relative" id="scrollbar1">
                                            <template v-if="!isProcessing">
                                                <div v-if="skillTest && Object.keys(skillTest).length" class="row">
                                                    <button style="width: 100px"
                                                        type="button" class="btn rounded-0 fw-bold me-3 btn-warning">
                                                        {{ indexes.skillTest.toUpperCase() }}
                                                    </button>
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
                                                            <h5 class="text-center fw-bold text-danger">Status: {{ item.status.toUpperCase() }}</h5>
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
                            <button class="btn btn-primary rounded-0" data-bs-dismiss="modal">
                                Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</template>

<script>
import swal from "sweetalert2";
import VideoRecorder from "../layouts/VideoRecorder.vue";
import { mapActions, mapGetters } from "vuex";
export default {
    components: {
        VideoRecorder,
    },
    data() {
        return {
            skillTest: [],
            active: false,
            images: [],
            items: [],
            video: null,
            pageFlag: 0,
            isProcessing: false,
            letter: null,
            indexes: {
                alphabet: 0,
                skillTest: 'a'
            },
            selected: {
                object: null,
                letter: null
            },
            index: {
                letter: null,
            },
            flag: "alphabet-letters",
        };
    },
    created() {
        this.getFlags();
        this.getSkillTest()
    },
    beforeUnmount() {
        $('#skillTestModal').modal('hide')
    },
    computed: {
        ...mapGetters({
            data: "alphabetLetters",
            isLoading: "isLoading"
        }),
        count() {
            return this.data.length;
        },
        selectedAlphabet() {
            return this.data[this.indexes.alphabet];
        },
        selectedSkillTest() {
            return this.skillTest[this.indexes.skillTest] ?? []
        },
    },
    methods: {
        ...mapActions({
            getFlags: "setAlphabetLetters",
        }),
        navigate() {
            this.$router.push("/student-textbook");
        },
        setVideo() {
            this.video = this.selectedAlphabet.video_url;
            var video = document.getElementById("myVideo");
            video.setAttribute("src", this.video); // set the new video src
            video.load(); // load the new video
            video.play(); // play the new video
            this.selected.letter = this.selectedAlphabet.letter;
            this.selected.object = this.selectedAlphabet.object;
            $("#alphabetsModal").modal("show");
            // if (!this.selectedAlphabet.isDone) {
            //     this.updateFlag()
            // }
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
        alphabetHandler(index, letter) {
            this.indexes.skillTest = letter
            this.indexes.alphabet = index;
        },
        letterHandler(letter) {
            this.indexes.skillTest = letter
        },
        setUploadSuccess() {
            $("#alphabetsModal").modal("hide");
            $("#recordVideoModal").modal("hide");
            swal.fire('Success', 'Skill test video has been uploaded successfully.', 'success')
            this.getSkillTest()
        },
        async updateFlag() {
            await axios
                .put(`/api/update-flag/alphabet-letters`, this.index)
                .then((response) => {
                    this.getFlags();
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    this.isProcessing = false;
                });
        },
    },
};
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
    transition: transform 0.2s ease-in-out;
}

.image-button:hover {
    border: 1px solid gray;
    transform: scale(1.1);
}

.title-header {
    font-family: "Bangers", cursive;
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

.st-width {
    width: 320px;
}
</style>
