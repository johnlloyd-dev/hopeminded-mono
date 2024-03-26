<template>
    <div>
        <div class="row d-flex align-items-center mb-3 mt-5">
            <div class="col-2">
                <h3 class="fw-bold">Numbers</h3>
            </div>
            <div class="col-2">
                <h3 class="fw-bold">Chapter {{ selectedChapter }}</h3>
            </div>
            <div class="col-8">
                <button data-bs-toggle="modal" data-bs-target="#addNumberModal" class="btn btn-success rounded-0 fw-bold">
                    Add Number
                </button>
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr class="bg-secondary">
                        <th class="text-white">Name</th>
                        <th class="text-white">Value</th>
                        <th class="text-white">Object</th>
                        <th class="text-white">Image Path</th>
                        <th class="text-white">Video Path</th>
                        <th class="text-white">Action</th>
                    </tr>
                </thead>
                <tbody v-if="numbers.length == 0">
                    <tr>
                        <td colspan="5" class="text-center fw-bold">No data found</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr v-for="item in data" :key="item.id">
                        <td class="fw-bold">{{ item.name.toString().toUpperCase() }}</td>
                        <td class="fw-bold">{{ item.value }}</td>
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
        <div v-if="numbers.length !== 0" class="d-flex justify-content-around">
            <button :disabled="flag == 0" @click="prev()" class="btn btn-secondary rounded-0">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button :disabled="flag == (numbers.length - 1)" @click="next()" class="btn btn-secondary rounded-0">
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
        <div class="modal fade" id="addNumberModal" data-bs-backdrop="static" tabindex="-1"
            aria-labelledby="addNumberModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <img width="70" src="/images/main-logo.png" style="margin-right: 10px; border-radius: 50%" class="logo" alt="Hopeminded Logo">
                        <h5 class="modal-title" id="addNumberModalLabel">Add Number</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="value" class="form-label fw-bold">Number Value</label>
                                <input v-model="numberContent.value" maxlength="2" type="text" class="form-control" id="value"
                                    aria-describedby="value">
                                <small class="text-danger" v-if="errors && errors.value">{{ errors.value[0] }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="_n" class="form-label fw-bold">Object Name</label>
                                <input v-model="numberContent.object_name" type="text" class="form-control" id="_n"
                                    aria-describedby="_n">
                                <small class="text-danger" v-if="errors && errors.object_name">{{ errors.object_name[0]
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
                        <button :disabled="isProcessing" @click.prevent="submitNumber" type="button" class="btn-primary rounded-0 btn">
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
            isProcessing: false,
            numbers: [],
            data: [],
            flag: 0,
            media: null,
            image: null,
            video: null,
            textbookId: null,
            numberContent: {
                flag: null,
                value: null,
                object_name: null,
                image: null,
                video: null
            },
            errors: []
        }
    },
    computed: {
        selectedChapter() {
            return this.chapter
        }
    },
    watch: {
        selectedChapter() {
            this.getTextbooks()
        }
    },
    created() {
        this.numberContent.flag = this.$route.params.textbookFlag
        this.getTextbooks()
    },
    methods: {
        async getTextbooks() {
            const { data } = await axios.get('/api/textbook/numbers', {
                params: {
                    user: 'teacher',
                    chapter: this.selectedChapter
                }
            })
            this.numbers = _.chunk(data, 5)
            this.data = this.numbers[this.flag]
        },
        prev() {
            if (this.flag != 0) {
                this.flag--
                this.data = this.numbers[this.flag]
            }
        },
        next() {
            if (this.flag != (this.numbers.length - 1)) {
                this.flag++
                this.data = this.numbers[this.flag]
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
            this.numberContent.image = event.target.files[0]
        },
        changeVideoFile(event) {
            this.numberContent.video = event.target.files[0]
        },
        async submitNumber() {
            this.errors = []
            const data = new FormData()
            data.append('flag', this.numberContent.flag)
            if(this.numberContent.value != null)
                data.append('value', this.numberContent.value)
            if(this.numberContent.object_name != null)
                data.append('object_name', this.numberContent.object_name)
            if(this.numberContent.image != null)
                data.append('image', this.numberContent.image)
            if(this.numberContent.video != null)
                data.append('video', this.numberContent.video)
            try {
                this.isProcessing = true
                const response = await axios.post('/api/textbook/number', data, {
                    params: {
                        chapter: this.selectedChapter
                    },
                    headers: {
                        'content-type': 'multipart/form-data'
                    }
                })
                if (response.status === 200) {
                    this.numberContent.value = null
                    this.numberContent.object_name = null
                    this.numberContent.image = null
                    this.numberContent.video = null
                    this.$refs.imageFileInput.value = '';
                    this.$refs.videoFileInput.value = '';
                    swal.fire('Success', response.data.message, 'success')
                    this.getTextbooks()
                    $('#addNumberModal').modal('hide');
                }
            } catch (error) {
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
                const response = await axios.delete(`/api/textbook/number/${this.textbookId}`)
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
</style>
