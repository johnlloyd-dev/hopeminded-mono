<template>
    <div>
        <div class="row d-flex align-items-center mt-5">
            <div class="col-4">
                <h3 class="fw-bold">Vowels/Consonants</h3>
            </div>
        </div>
        <div class="row d-flex align-items-center mb-3 mt-3">
            <div class="col-4">
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" v-model="alphabetFlag" :value="0" id="btnradio1"
                        autocomplete="off" :checked="alphabetFlag == 0">
                    <label class="btn btn-outline-dark fw-bold rounded-0"
                        :class="alphabetFlag == 0 ? 'text-white' : 'text-black'" for="btnradio1">Vowels</label>

                    <input type="radio" class="btn-check" v-model="alphabetFlag" :value="1" id="btnradio2"
                        autocomplete="off" :checked="alphabetFlag == 1">
                    <label class="btn btn-outline-dark fw-bold rounded-0"
                        :class="alphabetFlag == 1 ? 'text-white' : 'text-black'" for="btnradio2">Consonants</label>
                </div>
            </div>
            <div class="col-8">
                <button data-bs-toggle="modal" data-bs-target="#addAlphabetModal" class="btn btn-success rounded-0 fw-bold">
                    Add Alphabet
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3">
            <table class="table table-bordered table-responsive table-striped">
                <thead>
                    <tr class="bg-secondary">
                        <th class="text-white" style="font-size: 20px" scope="col">Letter</th>
                        <th class="text-white" style="font-size: 20px">Object</th>
                        <th class="text-white" style="font-size: 20px">Image Path</th>
                        <th class="text-white" style="font-size: 20px">Video Path</th>
                        <th class="text-white" style="font-size: 20px">Action</th>
                    </tr>
                </thead>
                <tbody v-if="vowelConsonants.length == 0">
                    <tr>
                        <td colspan="4" class="text-center fw-bold">No data found</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr v-for="item in data" :key="item.id">
                        <td class="fw-bold">{{ item.letter.toString().toUpperCase() }}</td>
                        <td class="fw-bold">{{ item.object }}</td>
                        <td>
                            <div class="row">
                                <div class="col-6">
                                    <span class="fw-bold">
                                        {{ item.image }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <button @click="viewMedia('image', item.image)"
                                        class="btn btn-sm btn-primary rounded-0">
                                        <i class="fas fa-external-link-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="fw-bold">
                            <div class="row">
                                <div class="col-6">
                                    <span class="fw-bold">
                                        {{ item.video }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <button @click="viewMedia('video', item.video)"
                                        class="btn btn-sm btn-primary rounded-0">
                                        <i class="fas fa-external-link-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="d-flex justify-content-center">
                            <button class="btn-danger btn rounded-0 btn-sm">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="vowelConsonants.length !== 0 && alphabetFlag == 1" class="d-flex justify-content-around">
            <button @click="prev()" class="btn btn-secondary rounded-0">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button @click="next()" class="btn btn-secondary rounded-0">
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
                            <div class="mb-3">
                                <label for="letter" class="form-label fw-bold">Alphabet</label>
                                <input v-model="alphabetContent.letter" type="text" class="form-control" id="letter"
                                    aria-describedby="letter">
                            </div>
                            <div class="mb-3">
                                <label for="objectName" class="form-label fw-bold">Object Name</label>
                                <input v-model="alphabetContent.objectName" type="text" class="form-control" id="objectName"
                                    aria-describedby="objectName">
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label fw-bold">Image File</label>
                                <input type="file" class="form-control" id="image">
                            </div>
                            <div class="mb-3">
                                <label for="video" class="form-label fw-bold">Video File</label>
                                <input type="file" class="form-control" id="video">
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
            vowelConsonants: [],
            alphabetFlag: 0,
            data: [],
            newData: [],
            flag: 0,
            media: null,
            image: null,
            video: null,
            alphabetContent: {
                letter: '',
                objectName: null,
                image: null,
                video: null
            },
            errors: []
        }
    },
    created() {
        this.getTextbooks()
    },
    watch: {
        alphabetFlag(newValue, oldValue) {
            this.resetContent(newValue, oldValue)
        }
    },
    methods: {
        async getTextbooks() {
            const vowelConsonants = await axios.get('/storage/json/vowel-consonants.json')
            this.vowelConsonants = vowelConsonants.data
            this.newData = this.vowelConsonants[this.alphabetFlag]
            this.data = this.newData
        },
        prev() {
            if (this.flag != 0) {
                this.flag--
                this.data = this.newData[this.flag]
            }
        },
        next() {
            if (this.flag != (this.vowelConsonants[this.alphabetFlag].length - 1)) {
                this.flag++
                this.data = this.newData[this.flag]
            }
        },
        resetContent(newValue, oldValue) {
            if (newValue == 1) {
                this.data = []
                this.newData = _.chunk(this.vowelConsonants[newValue], 5)
                this.data = this.newData[this.flag]
            } else {
                this.data = []
                this.newData = this.vowelConsonants[newValue]
                this.data = this.newData
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
</style>