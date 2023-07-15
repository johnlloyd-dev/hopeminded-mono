<template>
    <div>
        <div v-if="chunks.length === 0" class="row w-100">
            <div class="col-12 text-center">
                <video class="video-width" ref="videoElement" autoplay></video>
            </div>
            <div class="col-6 text-center">
                <button type="button" class="btn btn-secondary rounded-0" @click="startRecording"
                    :disabled="recording">Start Recording</button>
            </div>
            <div class="col-6 text-center">
                <button type="button" class="btn btn-secondary rounded-0" @click="stopRecording" :disabled="!recording">Stop
                    Recording</button>
            </div>
        </div>
        <div v-else class="row">
            <div class="col-12 text-center">
                <video class="video-width" ref="previewElement" :src="recordedVideoURL" controls></video>
            </div>
            <div class="row">
                <div class="col-6 text-center">
                    <button type="button" class="btn btn-primary rounded-0" @click="saveVideo">Save Video</button>
                </div>
                <div class="col-6 text-center">
                    <button type="button" class="btn btn-primary rounded-0" @click="retake">Retake</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['active'],
    data() {
        return {
            videoStream: null,
            mediaRecorder: null,
            chunks: [],
            recording: false,
            recordedVideoURL: ''
        };
    },
    watch: {
        active(isStart) {
            if (isStart) {
                this.setVideoRecorder()
            } else {
                if (this.videoStream) {
                    const tracks = this.videoStream.getTracks();
                    tracks.forEach((track) => track.stop());
                }
            }
        }
    },
    beforeUnmount() {
        if (this.videoStream) {
            const tracks = this.videoStream.getTracks();
            tracks.forEach((track) => track.stop());
        }
    },
    methods: {
        setVideoRecorder() {
            // Request access to the user's camera
            navigator.mediaDevices.getUserMedia({ video: true })
                .then((stream) => {
                    this.$refs.videoElement.srcObject = stream;
                    this.videoStream = stream;
                })
                .catch((error) => {
                    console.error('Error accessing camera:', error);
                });
        },
        retake() {
            this.chunks = []
            this.setVideoRecorder()
        },
        startRecording() {
            this.chunks = [];
            this.mediaRecorder = new MediaRecorder(this.videoStream, {
                mimeType: 'video/webm',
            });
            this.mediaRecorder.start()
            this.mediaRecorder.addEventListener('dataavailable', (event) => {
                if (event.data.size > 0) {
                    this.chunks.push(event.data);
                }
            });
            this.recording = true;
        },
        stopRecording() {
            this.mediaRecorder.stop();
            this.recording = false;
            this.mediaRecorder.addEventListener('stop', () => {
                this.generatePreview();
            });
        },
        generatePreview() {
            const blob = new Blob(this.chunks, { type: 'video/webm' });
            this.recordedVideoURL = URL.createObjectURL(blob);
            this.$refs.previewElement.src = this.recordedVideoURL;
        },
        async saveVideo() {
            const blob = new Blob(this.chunks, { type: 'video/webm' });
            const formData = new FormData();
            var currentTime = new Date();
            var filename = currentTime.getTime().toString();
            formData.append('video', blob, `ST${filename}.mp4`);
            formData.append('letter', this.$parent.selected.letter)
            formData.append('flag', this.$parent.flag)
            formData.append('object', this.$parent.selected.object)
            try {
                const response = await axios.post('/api/skill-test/upload', formData, {headers: { 'content-type': 'multipart/form-data'}})
                if(response.status == 200) {
                    this.$parent.active = false
                    const tracks = this.videoStream.getTracks();
                    tracks.forEach((track) => track.stop());
                    this.chunks = []
                    this.videoStream = null
                    this.mediaRecorder = null
                    this.recordedVideoURL = ''
                    this.$parent.setUploadSuccess()
                }
            } catch (error) {
                console.log(error)
            }
        },
    },
};
</script>

<style>
.video-width {
    width: 650px;
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
