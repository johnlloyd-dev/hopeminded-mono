<template>
    <div>
        <div class="row d-flex align-items-center mb-3 mt-5">
            <div class="col-4">
                <h3 class="fw-bold">Alphabet/Words</h3>
            </div>
            <div class="col-8">
                <button data-bs-toggle="modal" data-bs-target="#addAlphabetModal" class="btn btn-success rounded-0 fw-bold">
                    Add Alphabet
                </button>
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <table class="table table-bordered table-responsive table-striped">
                <thead>
                    <tr class="bg-secondary">
                        <th class="text-white" style="font-size: 20px" scope="col">Letter</th>
                        <th class="text-white" style="font-size: 20px">Media Path</th>
                    </tr>
                </thead>
                <tbody v-if="alphabetWords.length == 0">
                    <tr>
                        <td colspan="4" class="text-center fw-bold">No data found</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr v-for="item in data" :key="item.id">
                        <td class="fw-bold">{{ Object.keys(item)[0].toUpperCase() }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle rounded-0 px-2" type="button"
                                    id="dropdownMenuButton" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                    aria-expanded="false">
                                    <span class="fas fa-chevron-circle-down fa-lg mb-0"></span>
                                </button>
                                <div class="dropdown-menu" style="width: 55rem" aria-labelledby="dropdownMenuButton">
                                    <table class="table table-bordered table-responsive">
                                        <thead>
                                            <tr>
                                                <th scope="col">Object</th>
                                                <th>Image Path</th>
                                                <th>Video Path</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="media in Object.values(item)[0]" :key="media.image">
                                                <td>{{ media.object }}</td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <span>{{ media.image }}</span>
                                                        </div>
                                                        <div class="col-6">
                                                            <button @click="viewMedia('image', media.image)"
                                                                class="btn btn-sm btn-primary rounded-0">
                                                                <i class="fas fa-external-link-alt"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <span>{{ media.video }}</span>
                                                        </div>
                                                        <div class="col-6">
                                                            <button @click="viewMedia('video', media.video)"
                                                                class="btn btn-sm btn-primary rounded-0">
                                                                <i class="fas fa-external-link-alt"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="alphabetWords.length !== 0" class="d-flex justify-content-around">
            <button :disabled="flag == 0" @click="prev()" class="btn btn-secondary rounded-0">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button :disabled="flag == (alphabetWords.length - 1)" @click="next()" class="btn btn-secondary rounded-0">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <!-- View Media Modal -->
        <div class="modal fade" id="viewModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="viewModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewModalLabel">View Media</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center flex-column">
                            <img v-if="media == 'image'" :width="500" :src="image" alt="">
                            <video v-else id="myVideo" class="video-width" autoplay muted>
                                <source type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Alphabet Modal -->
        <div class="modal fade" id="addAlphabetModal" data-bs-backdrop="static" tabindex="-1"
            aria-labelledby="addAlphabetModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAlphabetModalLabel">Add Alphabet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row">
                                <div class="col-lg-10">
                                    <div class="mb-3">
                                        <label for="letter" class="form-label fw-bold">Alphabet</label>
                                        <input v-model="alphabetContent.letter" type="text" class="form-control" id="letter"
                                            aria-describedby="letter">
                                    </div>
                                </div>
                            </div>
                            <div v-for="num, index in alphabetContent" :key="index">
                                <hr style="width: 39.5rem">
                                <div class="row">
                                    <div class="col-10 rounded-2 p-2">
                                        <div class="mb-3">
                                            <label for="objectName" class="form-label fw-bold">Object Name</label>
                                            <input v-model="alphabetContent.objectName" type="text" class="form-control"
                                                id="objectName" aria-describedby="objectName">
                                        </div>
                                        <div class="mb-3">
                                            <label for="image" class="form-label fw-bold">Image File</label>
                                            <input type="file" class="form-control" id="image">
                                        </div>
                                        <div class="mb-3">
                                            <label for="video" class="form-label fw-bold">Video File</label>
                                            <input type="file" class="form-control" id="video">
                                        </div>
                                    </div>
                                    <div class="col-2 d-flex align-items-center justify-content-around">
                                        <button :disabled="alphabetContent.length == 5" @click="manageFields('plus')"
                                            class="btn btn-success rounded-0" type="button">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button :disabled="alphabetContent.length == 1"
                                            @click="manageFields('minus', index)" class="btn btn-danger rounded-0"
                                            type="button">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            alphabetWords: [],
            data: [],
            flag: 0,
            count: 1,
            media: null,
            image: null,
            video: null,
            alphabetContent: [{
                letter: '',
                objectName: null,
                image: null,
                video: null
            }],
            errors: []
        }
    },
    created() {
        this.getTextbooks()
    },
    computed: {
        isModalOpen() {
            if($('#addAlphabetModal').hasClass('show')) return true;
            else return false;
        }
    },
    methods: {
        async getTextbooks() {
            const alphabetWords = await axios.get('/storage/json/alphabets-with-words.json')
            this.alphabetWords = _.chunk(alphabetWords.data, 5)
            this.data = this.alphabetWords[this.flag]
        },
        manageFields(flag, index) {
            if (flag == 'plus' && this.alphabetContent.length <= 5) {
                this.alphabetContent.push({ letter: '', objectName: null, image: null, video: null })
            } else {
                if (this.alphabetContent.length != 1) {
                    this.alphabetContent.splice(index, 1)
                }
            }
        },
        prev() {
            if (this.flag != 0) {
                this.flag--
                this.data = this.alphabetWords[this.flag]
            }
        },
        next() {
            if (this.flag != (this.alphabetWords.length - 1)) {
                this.flag++
                this.data = this.alphabetWords[this.flag]
            }
        },
        viewMedia(flag, media) {
            this.media = flag
            if (flag == 'image') {
                this.image = media
            } else {
                this.video = media
                var video2 = document.getElementById("myVideo");
                video2.setAttribute("src", media); // set the new video src
                video2.load(); // load the new video
                video2.play(); // play the new video
            }
            $('#viewModal').modal('show')
        },
        navigate() {
            this.$router.push('/textbook-management')
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
}

.dropdown-toggle::after {
    display: none !important;
}
</style>