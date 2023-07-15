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
                                <h5 class="fw-bold">Vowels</h5>
                                <template v-if="data[0].length">
                                    <div class="col-12 mb-3" v-for="(item, index) in data[0]" :key="index">
                                        <button @click="alphabetHandler(index, 'vowel', item.letter)" style="width: 100px" type="button"
                                            class="btn rounded-0 fw-bold me-3"
                                            :class="indexes.vowel === index ? 'btn-warning btn-lg' : 'btn-success'">
                                            {{ item.letter.toUpperCase() }}
                                        </button>
                                        <!-- <span v-if="item.isDone"><i class="fas fa-check fa-xl"></i></span> -->
                                    </div>
                                </template>
                                <div v-else>
                                    <p class="fw-bold">No vowel letters added</p>
                                </div>
                                <hr class="w-75">
                                <h5 class="fw-bold">Consonants</h5>
                                <template v-if="data[1].length">
                                    <div class="col-12 mb-3" v-for="(item, index) in data[1]" :key="index">
                                        <button @click="alphabetHandler(index, 'consonant', item.letter)" style="width: 100px"
                                            type="button" class="btn rounded-0 fw-bold me-3"
                                            :class="indexes.consonant === index ? 'btn-warning btn-lg' : 'btn-success'">
                                            {{ item.letter.toUpperCase() }}
                                        </button>
                                        <!-- <span v-if="item.isDone"><i class="fas fa-check fa-xl"></i></span> -->
                                    </div>
                                </template>
                                <div v-else>
                                    <p class="fw-bold">No vowel letters added</p>
                                </div>
                                <hr class="w-75">
                                <button :disabled="Object.keys(skillTest).length < 5" @click="$router.push('/quiz-typing-balloon')" class="btn btn-danger btn-lg rounded-0 w-75 fw-bold">Take Quiz</button>
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
                                <img style="width: 60%" class="rounded box-shadow" :src="selectedAlphabet.image_url"
                                    alt="" />
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
                            <!-- <div class="col-6">
                            </div> -->
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
                        <h5 class="modal-title" id="exampleModalToggleLabel2">Skill Test</h5>
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
            images: [],
            items: [],
            skillTest: [],
            video: null,
            pageFlag: 0,
            isConsonant: false,
            isProcessing: false,
            letter: null,
            indexes: {
                vowel: 0,
                consonant: null,
                skillTest: 'a'
            },
            selected: {
                object: null,
                letter: null
            },
            index: {
                letter: null
            },
            flag: 'vowel-consonants'
        }
    },
    created() {
        this.getFlags()
        this.getSkillTest()
    },
    computed: {
        ...mapGetters({
            data: 'vowelConsonants',
            isLoading: "isLoading"
        }),
        count() {
            return this.data.length;
        },
        selectedAlphabet() {
            if (this.data.length) {
                if (this.indexes.vowel !== null) {
                    return this.data[0][this.indexes.vowel];
                } else {
                    return this.data[1][this.indexes.consonant];
                }
            }
        },
        selectedSkillTest() {
            return this.skillTest[this.indexes.skillTest] ?? []
        },
    },
    methods: {
        ...mapActions({
            getFlags: 'setVowelConsonants'
        }),
        navigate() {
            this.$router.push('/student-textbook')
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
        alphabetHandler(index, flag, letter) {
            if (flag === 'vowel') {
                this.indexes.vowel = index;
                this.indexes.consonant = null
            } else {
                this.indexes.vowel = null;
                this.indexes.consonant = index
            }
            this.indexes.skillTest = letter
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
            await axios.put(`/api/update-flag/vowel-consonants`, this.index)
                .then(response => {
                    this.getFlags({ isConsonant: this.isConsonant, pageFlag: this.pageFlag, counter: 0 })
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

.video-width {
    width: 900px;
}

.toggleWrapper {
    overflow: hidden;
    padding: 0 200px;

    input {
        position: absolute;
        left: -99em;
    }
}

.toggle {
    cursor: pointer;
    display: inline-block;
    position: relative;
    width: 90px;
    height: 50px;
    background-color: #83D8FF;
    border-radius: 90px - 6;
    transition: background-color 200ms cubic-bezier(0.445, 0.05, 0.55, 0.95);

    &:before {
        content: 'VOWELS';
        position: absolute;
        font-weight: bolder;
        left: -75px;
        top: 15px;
        font-size: 18px;
    }

    &:after {
        content: '';
        position: absolute;
        font-weight: bolder;
        right: -130px;
        top: 15px;
        font-size: 18px;
    }
}

.box-shadow {
    box-shadow: 10px 10px 5px gray;
}

.day-background {
    background-image: url("/images/background.jpg");
}

.night-background {
    background-image: url("/images/night-background.jpg");
}

.logo {
    box-shadow: rgba(240, 46, 170, 0.4) -5px 5px, rgba(240, 46, 170, 0.3) -10px 10px, rgba(240, 46, 170, 0.2) -15px 15px, rgba(240, 46, 170, 0.1) -20px 20px, rgba(240, 46, 170, 0.05) -25px 25px;
}

.toggle__handler {
    display: inline-block;
    position: relative;
    z-index: 1;
    top: 3px;
    left: 3px;
    width: 50px - 6;
    height: 50px - 6;
    background-color: #FFCF96;
    border-radius: 50px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, .3);
    transition: all 400ms cubic-bezier(0.68, -0.55, 0.265, 1.55);
    transform: rotate(-45deg);

    .crater {
        position: absolute;
        background-color: #E8CDA5;
        opacity: 0;
        transition: opacity 200ms ease-in-out;
        border-radius: 100%;
    }

    .crater--1 {
        top: 18px;
        left: 10px;
        width: 4px;
        height: 4px;
    }

    .crater--2 {
        top: 28px;
        left: 22px;
        width: 6px;
        height: 6px;
    }

    .crater--3 {
        top: 10px;
        left: 25px;
        width: 8px;
        height: 8px;
    }
}

.star {
    position: absolute;
    background-color: #ffffff;
    transition: all 300ms cubic-bezier(0.445, 0.05, 0.55, 0.95);
    border-radius: 50%;
}

.star--1 {
    top: 10px;
    left: 35px;
    z-index: 0;
    width: 30px;
    height: 3px;
}

.star--2 {
    top: 18px;
    left: 28px;
    z-index: 1;
    width: 30px;
    height: 3px;
}

.star--3 {
    top: 27px;
    left: 40px;
    z-index: 0;
    width: 30px;
    height: 3px;
}

.star--4,
.star--5,
.star--6 {
    opacity: 0;
    transition: all 300ms 0 cubic-bezier(0.445, 0.05, 0.55, 0.95);
}

.star--4 {
    top: 16px;
    left: 11px;
    z-index: 0;
    width: 2px;
    height: 2px;
    transform: translate3d(3px, 0, 0);
}

.star--5 {
    top: 32px;
    left: 17px;
    z-index: 0;
    width: 3px;
    height: 3px;
    transform: translate3d(3px, 0, 0);
}

.star--6 {
    top: 36px;
    left: 28px;
    z-index: 0;
    width: 2px;
    height: 2px;
    transform: translate3d(3px, 0, 0);
}

input:checked {
    +.toggle {
        background-color: #749DD6;

        &:before {
            content: "";
        }

        &:after {
            content: "CONSONANTS";
            color: #ffffff;
        }

        .toggle__handler {
            background-color: #FFE5B5;
            transform: translate3d(40px, 0, 0) rotate(0);

            .crater {
                opacity: 1;
            }
        }

        .star--1 {
            width: 2px;
            height: 2px;
        }

        .star--2 {
            width: 4px;
            height: 4px;
            transform: translate3d(-5px, 0, 0);
        }

        .star--3 {
            width: 2px;
            height: 2px;
            transform: translate3d(-7px, 0, 0);
        }

        .star--4,
        .star--5,
        .star--6 {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }

        .star--4 {
            transition: all 300ms 200ms cubic-bezier(0.445, 0.05, 0.55, 0.95);
        }

        .star--5 {
            transition: all 300ms 300ms cubic-bezier(0.445, 0.05, 0.55, 0.95);
        }

        .star--6 {
            transition: all 300ms 400ms cubic-bezier(0.445, 0.05, 0.55, 0.95);
        }
    }
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
