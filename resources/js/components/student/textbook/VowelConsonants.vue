<template>
    <div class="body position-relative" :class="isConsonant ? 'night-background' : 'day-background'">
        <button type="button" @click="navigate" class="btn btn-secondary mt-5 mx-5 rounded-0 position-absolute top-0 start-0">
            Back
        </button>
        <div v-if="data.length != 0" class="p-5">
            <h3 class="text-center title-header" :class="isConsonant ? 'text-white' : 'text-black'">Vowels/Consonants</h3>
            <div class="row w-100">
                <div class="col-lg-3 mt-2 mb-4">
                    <div class="text-center" v-if="isConsonant && pageFlag != 0">
                        <button class="btn btn-success rounded-0 btn-lg" @click="setPrevButton()">
                            Previous
                        </button>
                    </div>
                </div>
                <div class="col-lg-6 mt-2">
                    <div class="d-flex justify-content-center">
                        <div class="toggleWrapper">
                            <input type="checkbox" v-model="isConsonant" class="dn" id="dn" />
                            <label for="dn" class="toggle">
                                <span class="toggle__handler">
                                    <span class="crater crater--1"></span>
                                    <span class="crater crater--2"></span>
                                    <span class="crater crater--3"></span>
                                </span>
                                <span class="star star--1"></span>
                                <span class="star star--2"></span>
                                <span class="star star--3"></span>
                                <span class="star star--4"></span>
                                <span class="star star--5"></span>
                                <span class="star star--6"></span>
                            </label>
                        </div>
                    </div>
                    <p class="text-center" :class="isConsonant ? 'text-white' : 'text-black'">Switch between vowels and consonants.</p>
                </div>
                <div class="col-lg-3 mt-2 mb-4">
                    <div class="text-center" v-if="isConsonant && pageFlag != 3">
                        <button class="btn btn-success rounded-0 btn-lg" @click="setNextButton()">
                            Next
                        </button>
                    </div>
                </div>
            </div>
            <div v-if="!isConsonant" class="row w-100 justify-content-center mr-0">
                <div class="col-lg-4 col-md-6 text-center mb-4" v-for="(item, index) in data" :key="index">
                    <button @click="setVideo(index, item)" class="btn rounded image-button p-0" type="button">
                        <img width="300" height="200" :class="{ 'opacity-75': item.isDone, 'box-shadow': !item.isDone }" class="rounded" :src="item.image_url" alt="" />
                    </button>
                </div>
            </div>
            <div v-else class="row w-100 justify-content-center mr-0">
                <div class="col-lg-4 col-md-6 text-center mb-4" v-for="(item, index) in data" :key="index">
                    <button @click="setVideo(index, item)" class="btn rounded image-button p-0" type="button">
                        <img width="300" height="200" :class="{ 'opacity-75': item.isDone, 'box-shadow': !item.isDone }" class="rounded" :src="item.image_url" alt="" />
                    </button>
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
                                <video id="myVideo" class="video-width" autoplay muted>
                                    <source :src="video" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h3 v-else class="position-absolute top-50 start-50 translate-middle fw-bold">No records found</h3>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
    data() {
        return {
            images: [],
            items: [],
            video: null,
            pageFlag: 0,
            isConsonant: false,
            isProcessing: false,
            letter: null,
            index: {
                letter: null
            },
        }
    },
    created() {
        this.getFlags({isConsonant: this.isConsonant, pageFlag: this.pageFlag, counter: 0})
    },
    computed: {
        ...mapGetters({
            data: 'vowelConsonants'
        })
    },
    watch: {
        isConsonant(oldValue, newValue) {
            this.getFlags({isConsonant: oldValue, pageFlag: this.pageFlag, counter: 1})
        }
    },
    methods: {
        ...mapActions({
            getFlags: 'setVowelConsonants'
        }),
        navigate() {
            this.$router.push('/student-textbook')
        },
        setVideo(index, item) {
            this.video = item.video_url
            $('#alphabetsModal').modal('show')
            var video = document.getElementById("myVideo");
            video.setAttribute("src", this.video); // set the new video src
            video.load(); // load the new video
            video.play(); // play the new video
            this.index.letter = item.letter
            if(!item.isDone) {
                this.updateFlag()
            }
        },
        setPrevButton() {
            this.pageFlag--
            this.getFlags({isConsonant: this.isConsonant, pageFlag: this.pageFlag, counter: 1})
        },
        setNextButton() {
            if (this.pageFlag != (this.data.length - 1)) {
                this.pageFlag++
                this.getFlags({isConsonant: this.isConsonant, pageFlag: this.pageFlag,counter: 1})
            }
        },
        async updateFlag() {
            await axios.put(`/api/update-flag/vowel-consonants`, this.index)
            .then(response => {
                this.getFlags({isConsonant: this.isConsonant, pageFlag: this.pageFlag, counter: 0})
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
</style>