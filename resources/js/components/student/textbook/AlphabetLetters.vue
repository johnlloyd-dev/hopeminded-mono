<template>
    <div class="body position-relative">
        <button type="button" @click="navigate" class="btn btn-secondary mt-5 mx-5 rounded-0 position-absolute top-0 start-0">
            Back
        </button>
        <div v-if="data && data.length != 0" class="p-5">
            <h3 class="text-center title-header">Alphabets/Letters</h3>
            <div class="row w-100">
                <div class="col-lg-6 mt-2 mb-4">
                    <div class="text-center" v-if="pageFlag != 0">
                        <button class="btn btn-success rounded-0 btn-lg" @click="setPrevButton()">
                            Previous
                        </button>
                    </div>
                </div>
                <div class="col-lg-6 mt-2 mb-4">
                    <div class="text-center" v-if="pageFlag != 4">
                        <button class="btn btn-success rounded-0 btn-lg" @click="setNextButton()">
                            Next
                        </button>
                    </div>
                </div>
            </div>
            <div class="row w-100 justify-content-center mr-0">
                <div class="col-lg-4 col-md-6 text-center mb-4" v-for="(item, index) in data[pageFlag]" :key="index">
                    <button @click="setVideo(index, item)" class="btn rounded image-button p-0" type="button">
                        <img width="300" height="200" class="rounded" :class="{ 'opacity-75': item.isDone, 'box-shadow': !item.isDone }" :src="item.image_url" alt="" />
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
                                    <source type="video/mp4">
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
            isProcessing: false,
            letter: null,
            index: {
                letter: null
            },
            flag: 'alphabet-letters',
        }
    },
    created() {
        this.getFlags()
    },
    computed: {
        ...mapGetters({
            data: 'alphabetLetters'
        }),
        count() {
            return this.data.length
        }
    },
    methods: {
        ...mapActions({
            getFlags: 'setAlphabetLetters'
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
            this.items = this.data[this.pageFlag]
        },
        setNextButton() {
            if (this.pageFlag != (this.images.length - 1)) {
                this.pageFlag++
                this.items = this.data[this.pageFlag]
            }
        },
        async updateFlag() {
            await axios.put(`/api/update-flag/alphabet-letters`, this.index)
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
</style>
