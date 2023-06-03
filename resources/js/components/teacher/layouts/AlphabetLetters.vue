<template>
    <div>
        <div class="row d-flex align-items-center mb-3 mt-5">
            <div class="col-4">
                <h3 class="fw-bold">Alphabet/Letters</h3>
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
                        <th class="text-white" style="font-size: 20px">Letter</th>
                        <th class="text-white" style="font-size: 20px">Object</th>
                        <th class="text-white" style="font-size: 20px">Image Path</th>
                        <th class="text-white" style="font-size: 20px">Video Path</th>
                    </tr>
                </thead>
                <tbody v-if="alphabetLetters.length == 0">
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
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="alphabetLetters.length !== 0" class="d-flex justify-content-around">
            <button :disabled="flag == 0" @click="prev()" class="btn btn-secondary rounded-0">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button :disabled="flag == (alphabetLetters.length - 1)" @click="next()" class="btn btn-secondary rounded-0">
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
                                <input v-model="alphabetContent.letter" maxlength="1" type="text" class="form-control" id="letter"
                                    aria-describedby="letter">
                                <small class="text-danger" v-if="errors && errors.letter">{{ errors.letter[0] }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="objectName" class="form-label fw-bold">Object Name</label>
                                <input v-model="alphabetContent.objectName" type="text" class="form-control" id="objectName"
                                    aria-describedby="objectName">
                                <small class="text-danger" v-if="errors && errors.objectName">{{ errors.objectName[0]
                                }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label fw-bold">Image File</label>
                                <input ref="imageFileInput" accept="image/*" @change="changeImageFile" type="file" class="form-control" id="image">
                                <small class="text-danger" v-if="errors && errors.image">{{ errors.image[0] }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="video" class="form-label fw-bold">Video File</label>
                                <input ref="videoFileInput" @change="changeVideoFile"  accept="video/*" type="file" class="form-control" id="video">
                                <small class="text-danger" v-if="errors && errors.video">{{ errors.video[0] }}</small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button @click.prevent="submitAlphabet" type="button" class="btn-primary rounded-0 btn">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import swal from 'sweetalert2'
export default {
    data() {
        return {
            alphabetLetters: [],
            data: [],
            flag: 0,
            media: null,
            image: null,
            video: null,
            alphabetContent: {
                flag: null,
                letter: null,
                objectName: null,
                image: null,
                video: null
            },
            errors: []
        }
    },
    created() {
        this.alphabetContent.flag = this.$route.params.textbookFlag
        this.getTextbooks()
    },
    computed: {

    },
    methods: {
        async getTextbooks() {
            const alphabetLetters = await axios.get('/storage/json/alphabets-with-letters.json')
            this.alphabetLetters = _.chunk(alphabetLetters.data, 5)
            this.data = this.alphabetLetters[this.flag]
        },
        prev() {
            if (this.flag != 0) {
                this.flag--
                this.data = this.alphabetLetters[this.flag]
            }
        },
        next() {
            if (this.flag != (this.alphabetLetters.length - 1)) {
                this.flag++
                this.data = this.alphabetLetters[this.flag]
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
        },
        changeImageFile(event) {
            this.alphabetContent.image = event.target.files[0]
        },
        changeVideoFile(event) {
            this.alphabetContent.video = event.target.files[0]
        },
        async submitAlphabet() {
            this.errors = []
            const data = new FormData()
            data.append('flag', this.alphabetContent.flag)
            if(this.alphabetContent.letter != null)
                data.append('letter', this.alphabetContent.letter)
            if(this.alphabetContent.objectName != null)
                data.append('objectName', this.alphabetContent.objectName)
            if(this.alphabetContent.image != null)
                data.append('image', this.alphabetContent.image)
            if(this.alphabetContent.video != null)
                data.append('video', this.alphabetContent.video)
            try {
                const response = await axios.post('/api/textbook/add', data, {
                    headers: { 'content-type': 'multipart/form-data' }
                })
                if (response.status === 200) {
                    this.alphabetContent.flag = null
                    this.alphabetContent.letter = null
                    this.alphabetContent.objectName = null
                    this.alphabetContent.image = null
                    this.alphabetContent.video = null
                    this.$refs.imageFileInput.value = '';
                    this.$refs.videoFileInput.value = '';
                    swal.fire('Success', response.data.message, 'success')
                    this.getTextbooks()
                    this.$('#addAlphabetModal').modal('hide');
                }
            } catch (error) {
                this.errors = error.response.data.errors
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