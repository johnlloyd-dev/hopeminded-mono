<template>
    <body class="position-relative">
        <div class="card px-5 rounded-0 w-50">
            <div class="text-center">
                <img width="150" src="/images/main-logo.png" class="circle-image my-3" alt="Hopeminded Logo">
            </div>
            <h4 class="text-center mb-2 font-weight-bold title-header mt-3">Login</h4>
            <div class="card-body">
                <form>
                    <div class="alert alert-secondary" role="alert">
                        <div class="row mb-2">
                            <div class="col-lg-12 mb-3">
                                <label for="username" class="form-label text-dark" style="font-weight: bold">Username:</label>
                                <input type="text" v-model="auth.username" class="form-control" id="username">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.username">{{
                                    errors.username[0] }}</small>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="password" class="form-label text-dark" style="font-weight: bold">Password:</label>
                                <input type="password" v-model="auth.password" class="form-control" id="password">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.password">{{
                                    errors.password[0] }}</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button @click.prevent="login" v-if="!isLoading" style="font-weight: bold; width: 120px;"
                                type="button" class="btn btn-primary me-3">Login</button>
                            <button v-else disabled style="font-weight: bold; width: 120px;" type="button"
                                class="btn btn-primary pb-0 me-3">
                                <Loading />
                            </button>
                            <span>
                                <a style="font-weight: bold" type="button" class="cursor-pointer"
                                    @click="forgotPassword">Forgot
                                    password?</a></span>
                        </div>
                        <hr>
                        <h6 class="text-dark fw-bold">FOR STUDENTS</h6>
                        <span style="font-weight: bold" class="text-dark">Don't have an account yet? <a
                                class="cursor-pointer" style="font-weight: bold"
                                @click="redirectRegister">Register</a></span>
                    </div>
                </form>
                <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form @submit.prevent="sendForgotPasswordLink">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="forgotPasswordModalLabel">Enter Email</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label for="email" class="form-label" style="font-weight: bold">Email Address:</label>
                                    <input type="email" v-model="auth.email" required class="form-control" id="email">
                                    <small class="text-danger font-weight-bold" v-if="errors && errors.email">{{
                                        errors.email[0] }}</small>
                                </div>
                                <div class="modal-footer">
                                    <button v-if="!processing" type="submit" style="width: 230px;"
                                        class="btn btn-primary rounded-0">Send
                                        Password Reset Link</button>
                                    <button v-else disabled style="font-weight: bold; width: 230px;" type="button"
                                        class="btn btn-primary pb-0 me-3">
                                        <Loading />
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mt-5 text-center custom-background">
            <div class="row p-5">
                <div class="col-lg-12 d-flex align-items-center justify-content-center">
                    <h1 class="mt-3 fw-bolder text-white">How to use Hopeminded?</h1>
                </div>
                <div class="col-lg-12">
                    <div class="p-3 mb-3">
                        <iframe width="860" height="615" src="https://www.youtube.com/embed/nMLB0MFHJXk"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
        <footer-component class="w-100"></footer-component>
    </body>
</template>

<script>
import swal from 'sweetalert2'
import Loading from './loading/Loading.vue'
export default {
    name: "login",
    components: {
        Loading
    },
    data() {
        return {
            auth: {
                username: "",
                password: "",
                email: null,
            },
            processing: false,
            isLoading: false,
            errors: []
        }
    },
    beforeUnmount() {
        $('#forgotPasswordModal').modal('hide')
    },
    methods: {
        redirectRegister() {
            this.$router.push({ path: '/student-register' })
        },
        forgotPassword() {
            $('#forgotPasswordModal').modal('show')
        },
        async sendForgotPasswordLink() {
            try {
                this.errors = []
                this.processing = true
                const process = await axios.post('/api/reset-password/send-link', this.auth)
                if (process.status == 200) {
                    $('#forgotPasswordModal').modal('hide')
                    this.auth.email = null
                    swal.fire('Email Sent', process.data.message, 'success')
                }
            } catch (error) {
                this.errors = error.response.data
            } finally {
                this.processing = false
            }

        },
        async login() {
            this.isLoading = true
            this.errors = []
            await axios.post('api/login', this.auth)
                .then(({ data }) => {
                    localStorage.setItem('token', data.data.token)
                    localStorage.setItem('fullname', data.data.fullname)
                    console.log(data.data.user_flag)
                    if (data.data.user_flag == 'student')
                        this.$router.push({ path: '/student-dashboard' })
                    else if (data.data.user_flag == 'teacher')
                        this.$router.push({ path: '/student-management' })
                    else
                        this.$router.push({ path: '/teacher-management' })
                }).catch((error) => {
                    if (error.response.data.errors)
                        this.errors = error.response.data.errors
                    else
                        this.errors = error.response.data
                }).finally(() => {
                    this.isLoading = false
                })
        },
    }
}
</script>

<style scoped>
body {
    background-image: url("/images/sky-night.png");
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    font-family: "Arial", sans-serif;
    color: #333;
    padding: 0;
    margin: 0;
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow-x: hidden;
    /* justify-content: center; */
}

.card {
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
    width: 500px !important;
}

.cursor-pointer {
    cursor: pointer !important;
}

.title-header {
    font-family: 'Bangers', cursive;
    font-size: 30px;
}
.custom-background {
    position: relative;
    background-image: url('/images/night-background.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
}
</style>
