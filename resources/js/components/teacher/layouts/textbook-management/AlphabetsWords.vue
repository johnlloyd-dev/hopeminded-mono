<template>
    <div>
        <div class="row d-flex align-items-center mb-3 mt-5">
            <div class="col-2">
                <h3 class="fw-bold">Alphabet/Words</h3>
            </div>
            <div class="col-2">
                <h3 class="fw-bold">Chapter {{ selectedChapter }}</h3>
            </div>
            <div class="col-8">
                <button data-bs-toggle="modal" data-bs-target="#addAlphabetModal" class="btn btn-success rounded-0 fw-bold">
                    Add Alphabet
                </button>
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr class="bg-secondary">
                        <th class="text-white">Letter</th>
                        <th class="text-white">Object</th>
                        <th class="text-white">Image Path</th>
                        <th class="text-white">Video Path</th>
                        <th class="text-white">Action</th>
                    </tr>
                </thead>
                <tbody v-if="alphabetWords.length == 0">
                    <tr>
                        <td colspan="5" class="text-center fw-bold">No data found</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr v-for="item in data" :key="item.id">
                        <td class="fw-bold">{{ item.letter.toString().toUpperCase() }}</td>
                        <td class="fw-bold">{{ item.object }}</td>
                        <td>
                            <div class="row">
                                <div class="col-10">
                                    <p style="width: 350px;" class="fw-bold text-truncate">
                                        {{ item.image_url }}
                                    </p>
                                </div>
                                <div class="col-2">
                                    <button @click="viewMedia('image', item.image_url)"
                                        class="btn btn-sm btn-primary rounded-0">
                                        <i class="fas fa-external-link-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="fw-bold">
                            <div class="row">
                                <div class="col-10">
                                    <p style="width: 350px;" class="fw-bold text-truncate">
                                        {{ item.video_url }}
                                    </p>
                                </div>
                                <div class="col-2">
                                    <button @click="viewMedia('video', item.video_url)"
                                        class="btn btn-sm btn-primary rounded-0">
                                        <i class="fas fa-external-link-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="d-flex justify-content-center">
                            <button @click="deleteConfirmation(item.id)" class="btn-danger btn rounded-0 btn-sm">
                                <i class="fas fa-trash-alt"></i>
                            </button>
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
                        <img width="70" src="/images/main-logo.png" style="margin-right: 10px; border-radius: 50%" class="logo" alt="Hopeminded Logo">
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
                        <img width="70" src="/images/main-logo.png" style="margin-right: 10px; border-radius: 50%" class="logo" alt="Hopeminded Logo">
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
                        <button :disabled="isProcessing" @click.prevent="submitAlphabet" type="button" class="btn-primary rounded-0 btn">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete Textbook Modal -->
        <div class="modal fade" id="deleteTextbook" tabindex="-1" aria-labelledby="deleteTextbookLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTextbookLabel">Delete Textbook Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this textbook item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-0" data-bs-dismiss="modal">Close</button>
                    <button @click="deleteTextbook" type="button" class="btn btn-danger rounded-0">Delete <i class="fas fa-trash-alt"></i></button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import swal from 'sweetalert2'
export default {
    props: ['chapter'],
    data() {
        return {
            alphabetWords: [],
            fields: [0],
            nextId: 1,
            data: [],
            flag: 0,
            count: 1,
            media: null,
            image: null,
            video: null,
            error: null,
            popover: null,
            textbookId: null,
            alphabetContent: {
                flag: null,
                letter: null,
                objectName: null,
                image: null,
                video: null
            },
            errors: [],
            isProcessing: false
        }
    },
    created() {
        this.alphabetContent.flag = this.$route.params.textbookFlag
        this.getTextbooks()
        // axios.get(`/api/alphabets-words/get?user=${'teacher'}`)
    },
    beforeUnmount() {
        $('#addAlphabetModal').modal('hide')
    },
    computed: {
        errorsLength() {
            return Object.keys(this.errors).length
        },
        isModalOpen() {
            if ($('#addAlphabetModal').hasClass('show')) return true;
            else return false;
        },
        disabledButton() {
            const objectName = this.alphabetContent.objectName.filter(data =>  {
                return data == null
            })

            const image = this.alphabetContent.image.filter(data =>  {
                return data == null
            })

            const video = this.alphabetContent.video.filter(data =>  {
                return data == null
            })

            const letterNotMatch = this.alphabetContent.objectName.filter(data => {
                return data.charAt(0) != this.alphabetContent.letter
            })

            if(this.alphabetContent.letter == null || objectName.length > 0 || image.length > 0 || video.length > 0 || letterNotMatch.length > 0) {
                return true
            }

            return false
        },
        selectedChapter() {
            return this.chapter
        }
    },
    watch: {
        selectedChapter(newValue, oldValue) {
            this.getTextbooks()
        }
    },
    methods: {
        async getTextbooks() {
            const alphabetWords = await axios.get(`/api/alphabets-words/get?user=${'teacher'}&chapter=${this.selectedChapter}`)
            this.alphabetWords = _.chunk(alphabetWords.data, 5)
            this.data = this.alphabetWords[this.flag]
        },
        manageFields(flag) {
            if (flag == 'plus' && this.fields.length != 5) {
                this.fields.push(0)
                this.alphabetContent.objectName.push('')
                this.alphabetContent.image.push(null)
                this.alphabetContent.video.push(null)
            } else {
                if (this.fields.length != 1) {
                    this.fields.pop();
                    this.alphabetContent.objectName.pop()
                    this.alphabetContent.image.pop()
                    this.alphabetContent.video.pop()
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
            var video2 = document.getElementById("myVideo");
            this.media = flag
            if (flag == 'image') {
                this.image = media
            } else {
                this.video = media
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
            if (this.alphabetContent.letter != null)
                data.append('letter', this.alphabetContent.letter)
            if (this.alphabetContent.objectName != null)
                data.append('objectName', this.alphabetContent.objectName)
            if (this.alphabetContent.image != null)
                data.append('image', this.alphabetContent.image)
            if (this.alphabetContent.video != null)
                data.append('video', this.alphabetContent.video)
            try {
                this.isProcessing = true
                const response = await axios.post(`/api/textbook/add?chapter=${this.selectedChapter}`, data, {
                    headers: { 'content-type': 'multipart/form-data' }
                })
                if (response.status === 200) {
                    this.alphabetContent.letter = null
                    this.alphabetContent.objectName = null
                    this.alphabetContent.image = null
                    this.alphabetContent.video = null
                    this.$refs.imageFileInput.value = '';
                    this.$refs.videoFileInput.value = '';
                    swal.fire('Success', response.data.message, 'success')
                    this.getTextbooks()
                    $('#addAlphabetModal').modal('hide');
                }
            } catch (error) {
                console.log(error)
                this.errors = error.response.data.errors
            } finally {
                this.isProcessing = false
            }
        },
        deleteConfirmation(textbookId) {
            this.textbookId = textbookId
            $('#deleteTextbook').modal('show')
        },
        async deleteTextbook() {
            try {
                this.isProcessing = true
                const response = await axios.delete(`/api/textbook/delete/${this.textbookId}`)
                if(response.status == 200) {
                    $('#deleteTextbook').modal('hide');
                    swal.fire('Success', response.data.message, 'success')
                    this.getTextbooks()
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.isProcessing = false
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

.dropdown-toggle::after {
    display: none !important;
}
</style>
