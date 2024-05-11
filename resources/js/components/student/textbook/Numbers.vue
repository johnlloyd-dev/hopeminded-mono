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
                                    <button @click="numberHandler(index, item.value)" style="width: 100px" type="button"
                                        class="btn rounded-0 fw-bold me-3"
                                        :class="[hasSkillTest(item.value) ? 'btn-success' : 'btn-danger', { 'border border-white border-3 btn-lg': indexes.number === index }]">
                                        {{ item.value }}
                                    </button>
                                    <span v-if="item.isDone"><i class="fas fa-check fa-xl"></i></span>
                                </div>
                                <hr class="w-75">
                                <!-- <button :disabled="(Object.keys(skillTest).length < quantityRequirement?.value || !passedSkillTest) || (availableQuizRetakes.hasOwnProperty(filteredFlag) && availableQuizRetakes[filteredFlag].allowed_retake === 0)" -->
                                <!-- <button :disabled="Object.keys(skillTest).length < 5" -->
                                <button @click="$router.push('/quiz-matching-game')"
                                    class="btn btn-danger btn-lg rounded-0 w-75 fw-bold">{{ availableQuizRetakes.hasOwnProperty(filteredFlag) ? 'Retake Quiz' : 'Take Quiz' }}</button>
                                <h6 class="fw-bold" v-if="availableQuizRetakes.hasOwnProperty(filteredFlag)">Available Retake: <span class="text-danger">{{ availableQuizRetakes.hasOwnProperty(filteredFlag) ? availableQuizRetakes[filteredFlag].allowed_retake : 0 }}</span></h6>
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
                        <div v-if="selectedNumber">
                            <div class="row mt-5">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="text-center">
                                        <img style="width: 70%" class="box-shadow" :src="selectedNumber.image_url"
                                            alt="" />
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="d-flex align-items-center flex-column">
                                        <video id="myVideo" class="video-width box-shadow" autoplay muted controls loop>
                                            <source type="video/mp4" />
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <div class="d-grid gap-2">
                                    <button :disabled="availableSkillTestRetakes.hasOwnProperty(indexes.skillTest) && availableSkillTestRetakes[indexes.skillTest].allowed_retake === 0" type="button" @click="recordSkillTest()"
                                        class="btn btn-primary btn-lg rounded-0 fw-bold mt-3">
                                        {{ skillTest.hasOwnProperty(indexes.skillTest) ? 'Resubmit Skill Test' : 'Submit Skill Test' }} <i class="fas fa-external-link-alt"></i>
                                    </button>
                                    <h5 class="fw-bold text-center" v-if="skillTest.hasOwnProperty(indexes.skillTest)">Available Resubmit: <span class="text-danger">{{ availableSkillTestRetakes.hasOwnProperty(indexes.skillTest) ? availableSkillTestRetakes[indexes.skillTest].allowed_retake : 0 }}</span></h5>
                                    <!-- <a href="javascript:;" type="button" class="h5 text-dark text-center" @click="showTutorial">View Skill Test Tutorial</a> -->
                                    <div v-if="selectedSkillTest && !selectedSkillTest.length"
                                        class="alert alert-danger mb-0 mt-3 fw-bold text-center" role="alert">No skill test
                                        submitted for this alphabet yet.</div>
                                    <div v-else class="alert alert-success mb-0 mt-3 fw-bold text-center" role="alert">
                                        Uploaded Skill Test: {{ selectedSkillTest.length }}</div>
                                    <button :disabled="selectedSkillTest && !selectedSkillTest.length"
                                        data-bs-toggle="modal" data-bs-target="#skillTestModal" type="button"
                                        class="btn btn-warning btn-lg rounded-0 fw-bold">
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
                        <button @click="active = true" type="button" class="btn btn-primary rounded-0"
                            data-bs-target="#recordVideoModal" data-bs-toggle="modal" data-bs-dismiss="modal">
                            Record a skill test
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
                        <h5 class="modal-title" id="exampleModalToggleLabel2">Skill Test</h5>
                        <button @click="active = false" type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <video-recorder :active="active"></video-recorder>
                    </div>
                    <div class="modal-footer">
                        <button @click="active = false" class="btn btn-primary rounded-0" data-bs-toggle="dismiss"
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container mt-5">
                            <div class="row w-100">
                                <div class="col-2 border-end border-secondary py-3">
                                    <div class="scrollbar position-relative" id="scrollbar1">
                                        <template v-if="!isProcessing">
                                            <div v-if="skillTest && Object.keys(skillTest).length" class="row">
                                                <button style="width: 100px" type="button"
                                                    class="btn rounded-0 fw-bold me-3 btn-warning">
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
                                    <h5 class="fw-bold">Highest Score: <span class="text-danger">{{ highestScores[indexes.skillTest] ?? 0 }}</span></h5>
                                    <template v-if="!isProcessing">
                                        <div v-if="selectedSkillTest && selectedSkillTest.length">
                                            <div class="text-center">
                                                <div class="row">
                                                    <div v-for="(item, index) in selectedSkillTest" :key="index"
                                                        class="col-lg-4 col-md-6 text-center mb-4">
                                                        <div class="position-relative badge-overlay">
                                                            <h5 class="text-center fw-bold">{{ item.object + (index > 0 ? ' (Retake)' : '') }}</h5>
                                                            <video class="st-width" ref="previewElement"
                                                                :src="item.file_url" controls></video>
                                                        </div>
                                                        <span style="font-size: 20px">
                                                            Score: <span
                                                                class="text-center fw-bold text-danger">{{ item.score
                                                                }}/{{ item.perfect_score }}</span>
                                                        </span>
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

        <!-- Modal -->
        <div v-show="tutorialModal" class="modal fade" id="tutorialModal" tabindex="-1" aria-labelledby="tutorialModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-0">
                    <div class="modal-body position-relative">
                        <div class="position-absolute top-0 end-0">
                            <button type="button" class="btn-close border-0" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div id="carouselExampleIndicators" class="carousel slide" data-bs-touch="false" data-bs-interval="false">
                            <div class="carousel-inner">
                                <div class="carousel-item active position-relative" style="height: 520px;">
                                    <div class="position-absolute start-50 top-50 translate-middle">
                                        <h3 class="text-center">Welcome to Skill Test tutorial.</h3>
                                        <p class="text-center">This will help and guide you on how to record and submit your skill test.</p>
                                    </div>
                                </div>
                                <div class="carousel-item" style="height: 520px;">
                                    <img src="/images/tutorial-1.png" class="d-block w-100" alt="...">
                                    <p class="fw-bold h5 px-3 mt-3"><span class="text-danger fst-italic">First, </span>select any of the alphabet ang click the "Submit Skill Test" button as shown in the picture inside the red box.</p>
                                </div>
                                <div class="carousel-item" style="height: 520px;">
                                    <img src="/images/tutorial-2.png" class="d-block w-100" alt="...">
                                    <p class="fw-bold h5 px-3 mt-3"><span class="text-danger fst-italic">Second, </span>the recorder modal will popup and you can start recording by clicking the "Start Recording" button.</p>
                                    <p class="fw-bold h5 px-3 mt-3">Once you're finished recording, you can stop it by clicking the "Stop Recording" button.</p>
                                </div>
                                <div class="carousel-item" style="height: 520px;">
                                    <img src="/images/tutorial-3.png" class="d-block w-100" alt="...">
                                    <p class="fw-bold h5 px-3 mt-3"><span class="text-danger fst-italic">Third, </span>you will be given options to either retake or submit your recorded video.</p>
                                    <p class="fw-bold h5 px-3 mt-3">Clicking the "Retake" button let you record another video.</p>
                                </div>
                                <div class="carousel-item" style="height: 520px;">
                                    <img src="/images/tutorial-4.png" class="d-block w-100" alt="...">
                                    <p class="fw-bold h5 px-3 mt-3"><span class="text-danger fst-italic">Lastly, </span>once your skill test video submission is completed, a success message popup will appear.</p>
                                </div>
                                <div class="carousel-item" style="height: 520px;">
                                    <img src="/images/tutorial-5.png" class="d-block w-100" alt="...">
                                    <p class="fw-bold h5 px-3 mt-3">Once the overall process is successful, the alphabet button with submitted skill test will have a green background.</p>
                                    <p class="fw-bold h5 px-3 mt-3">You can also view/watch your skill test video.</p>
                                    <p class="fw-bold h2 px-3 mt-3 fst-italic">Thank You.</p>
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon bg-secondary rounded" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon bg-secondary rounded" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="position-absolute tutorial-button fw-bold">
            Skill Test Tutorial
            <button class="btn btn-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#tutorialModal">
                <i class="fas fa-question fa-lg"></i>
            </button>
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
            tutorialModal: false,
            images: [],
            items: [],
            video: null,
            pageFlag: 0,
            isProcessing: false,
            letter: null,
            highestScores: {},
            availableSkillTestRetakes: {},
            availableQuizRetakes: {},
            indexes: {
                number: 0,
                skillTest: 1
            },
            selected: {
                object: null,
                letter: null
            },
            index: {
                number: null,
            },
            flag: "numbers",
            selectedPercentage: {
                skill_test: 100
            },
        };
    },
    async created() {
        await this.getTextbookData();
        await this.getSkillTest()
        await this.getAvailableQuizRetakes()
        this.setVideo()
    },
    mounted() {
        const isTutorialDone = localStorage.getItem('tutorialFlag')
        if (!isTutorialDone) {
            this.tutorialModal = true
            $('#tutorialModal').modal('show')
            localStorage.setItem('tutorialFlag', true)
        }
        this.getQuantityRequirement('skill_test')
        this.getPassingPercentage()
    },
    beforeUnmount() {
        $('#skillTestModal').modal('hide')
        this.tutorialModal = false
    },
    computed: {
        ...mapGetters({
            data: "numbers",
            isLoading: "isLoading",
            quantityRequirement: 'quantityRequirement'
        }),
        count() {
            return this.data.length;
        },
        selectedNumber() {
            return this.data[this.indexes.number];
        },
        selectedSkillTest() {
            return this.skillTest[this.indexes.skillTest] ?? []
        },
        filteredFlag() {
            return this.flag.replace(/-/g, "_");
        },
        skillTestAverageScore() {
            if (Object.keys(this.highestScores).length) {
                const scores = Object.values(this.highestScores);
                const totalScore = scores.reduce(function (a, b) { return a + b; }, 0);
                const count = scores.length

                return {
                    score: totalScore / count
                };
            }
            return {
                score: 0
            };
        },
        passedSkillTest() {
            return this.calculatePercentage(this.skillTestAverageScore.score) >= this.selectedPercentage.skill_test
        }
    },
    methods: {
        ...mapActions({
            getTextbookData: "setNumbers",
            getQuantityRequirement: 'getQuantityRequiement'
        }),
        showTutorial() {
            this.tutorialModal = true
            $('#tutorialModal').modal('show')
        },
        navigate() {
            this.$router.push("/student-textbook");
        },
        setVideo() {
            this.video = this.selectedNumber.video_url;
            var video = document.getElementById("myVideo");
            video.setAttribute("src", this.video); // set the new video src
            video.load(); // load the new video
            video.play(); // play the new video
            this.selected.letter = this.selectedNumber.letter;
            this.selected.object = this.selectedNumber.object;
        },
        recordSkillTest() {
            this.active = true
            $('#recordVideoModal').modal('show')
        },
        async getSkillTest() {
            this.isProcessing = true
            try {
                const data = await axios.get(`/api/skill-test/fetch/${this.flag}`)
                const mergedObject = data.data.data.reduce((result, item) => {
                    const { letter, ...rest } = item;

                    if (result[letter]) {
                        result[letter].push(rest);
                    } else {
                        result[letter] = [rest];
                    }

                    return result;
                }, {});

                this.skillTest = mergedObject
                this.highestScores = data.data.highest_score ?? {}
                this.availableSkillTestRetakes = data.data.retake ?? {}
            } catch (error) {
                console.log(error)
            } finally {
                this.isProcessing = false
            }
        },
        async getAvailableQuizRetakes() {
            try {
                const response = await axios.get('/api/available-ratake/quiz/get')
                this.availableQuizRetakes = response.data
            } catch (error) {
                console.log(error)
            }

        },
        hasSkillTest(alphabet) {
            return this.skillTest.hasOwnProperty(alphabet)
        },
        numberHandler(index, letter) {
            this.indexes.skillTest = letter
            this.indexes.number = index;
            this.setVideo()
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
                    this.getTextbookData();
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    this.isProcessing = false;
                });
        },
        async getPassingPercentage() {
            try {
                const response = await axios.get(`/api/passing-percentage/get`)
                this.passingPercentage = response.data
                this.selectedPercentage.skill_test = response.data['skill_test'].percentage
            } catch (error) {
                console.log(error)
            }
        },
        calculatePercentage(score, perfectScore = 10) {
            return Math.round((score / perfectScore) * 100)
        }
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
    overflow-x: hidden;
    height: 100vh;
}

.help-ui {
    position: absolute;
    bottom: 50px;
    right: 50px;
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
    box-shadow: 10px 10px grey;
}

.video-width {
    width: 90%;
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
}</style>
