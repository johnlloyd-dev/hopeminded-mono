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
                        <th class="text-white" style="font-size: 20px" scope="col">Letter</th>
                        <th class="text-white" style="font-size: 20px">Media Path</th>
                        <th class="text-white" style="font-size: 20px">Action</th>
                    </tr>
                </thead>
                <tbody v-if="alphabetWords.length == 0">
                    <tr>
                        <td colspan="3" class="text-center fw-bold">No data found</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr v-for="item in data" :key="item.id">
                        <td class="fw-bold text-center">{{ Object.keys(item)[0].toUpperCase() }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle rounded-0 px-2 w-100" type="button"
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
                        <td class="d-flex justify-content-center h-100">
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
                            <div v-if="errorsLength > 0" class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    <li v-if="errors && errors.letter">{{ errors.letter[0] }}</li>
                                </ul>
                            </div>
                            <div class="mb-3">
                                <label for="letter" class="form-label fw-bold">Alphabet:</label>
                                <input v-model="alphabetContent.letter" ref="letterField" type="text" maxlength="1" class="form-control" id="letter"
                                    aria-describedby="letter">
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6 text-center">
                                    <button :disabled="fields.length == 5" @click="manageFields('plus')"
                                        class="btn btn-success rounded-0 fw-bold" type="button">
                                        Add field <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div class="col-6 text-center">
                                    <button :disabled="fields.length == 1"
                                        @click="manageFields('minus')" class="btn btn-danger rounded-0 fw-bold"
                                        type="button">
                                        Remove field <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div v-for="val, index in fields" :key="val">
                                <hr>
                                <div class="rounded-2 p-2">
                                    <div class="mb-3">
                                        <label for="objectName" class="form-label fw-bold">Object Name:</label>
                                        <input v-model="alphabetContent.objectName[index]" type="text" class="form-control"
                                            id="objectName" aria-describedby="objectName">
                                    </div>
                                    <div class="mb-3">
                                        <label for="image" class="form-label fw-bold">Image File:</label>
                                        <input :ref="'imageFileInput' + index" accept="image/*" @change="changeImageFile($event, index)" type="file"
                                            class="form-control" id="image">
                                    </div>
                                    <div class="mb-3">
                                        <label for="video" class="form-label fw-bold">Video File:</label>
                                        <input :ref="'videoFieldInput' + index" accept="video/*" @change="changeVideoFile($event, index)" type="file"
                                            class="form-control" id="video">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p class="fw-bold">Submit button is disabled due to following possible reasons:</p>
                            <ul>
                                <li><span class="text-danger text-justify fw-bold">Some fields have no values. Make sure to fill up all fields.</span> </li>
                                <li><span class="text-danger text-justify fw-bold">The first letter of the object name and the entered alphabet is not match.</span> </li>
                            </ul>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button :disabled="disabledButton" @click.prevent="submitAlphabet" type="button" class="btn-primary rounded-0 btn">
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
import { Popover } from 'bootstrap'
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
                letter: '',
                objectName: [''],
                image: [null],
                video: [null]
            },
            errors: []
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
            let newData = []
            alphabetWords.data.forEach(element => {
                var variableName = element.letter;
                var id = element.id;
                var value = element.attributes;

                var obj = {};
                obj[variableName] = value;
                obj.id = id;
                newData.push(obj);
            });
            this.alphabetWords = _.chunk(newData, 5)
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
        changeImageFile(event, index) {
            this.alphabetContent.image[index] = event.target.files[0]
        },
        changeVideoFile(event, index) {
            this.alphabetContent.video[index] = event.target.files[0]
        },
        async submitAlphabet() {
            this.errors = []
            const data = new FormData()
            data.append('flag', this.alphabetContent.flag)
            if (this.alphabetContent.letter != null) {
                data.append('letter', this.alphabetContent.letter)
            }
            data.append('objectName', JSON.stringify(this.alphabetContent.objectName))
            const videoFilter = this.alphabetContent.video.filter( data => {
                return data == null;
            });

            const imageFilter = this.alphabetContent.image.filter( data => {
                return data == null;
            });
            this.fields.forEach((element, index) => {
                if (videoFilter.length == 0 && imageFilter.length == 0) {
                    data.append('image[]', this.alphabetContent.image[index])
                    data.append('video[]', this.alphabetContent.video[index])
                }
            });

            const headers = { 'Content-Type': 'multipart/form-data' };
            try {
                const response = await axios.post(`/api/textbook/alphabets-words/add?chapter=${this.selectedChapter}`, data, {headers})
                if (response.status === 200) {
                    this.alphabetContent.letter = null
                    this.alphabetContent.objectName = ['']
                    this.alphabetContent.image = [null]
                    this.alphabetContent.video = [null]
                    this.files = [0]
                    swal.fire('Success', response.data.message, 'success')
                    this.getTextbooks()
                    $('#addAlphabetModal').modal('hide');
                }
            } catch (error) {
                console.log(error)
                this.errors = error.response.data.errors
                if(this.errors && this.errors.letter) {
                    this.$refs.letterField.focus()
                }
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
