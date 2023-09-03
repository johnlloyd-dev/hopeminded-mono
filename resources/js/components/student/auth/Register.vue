<template>
    <body class="position-relative">
        <button @click="$router.push('/')" class="btn btn-secondary rounded-0 position-absolute top-0 start-0">
            Back
        </button>
        <div class="card px-5 rounded-0 w-50">
            <div class="text-center">
                <img width="150" src="/images/main-logo.png" class="circle-image my-3" alt="Hopeminded Logo">
            </div>
            <h4 class="text-center mb-2 font-weight-bold title-header mt-3">Teacher Register</h4>
            <div :class="{ 'card-body': canAccess }">
                <form>
                    <div v-if="!canAccess">
                        <div class="row mb-2">
                            <div class="col-lg-12">
                                <h5 class="text-center">Please enter your Access ID.</h5>
                                <label for="accessId" style="font-weight: bold;" class="form-label">Access ID:</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">HM-</span>
                                    <input type="text" v-model="accessId" class="form-control" id="accessId">
                                </div>
                                <small class="text-danger font-weight-bold" v-if="errors && errors.access_id">{{
                                    errors.access_id[0] }}</small>
                            </div>
                        </div>
                        <div class="text-center mb-3">
                            <button v-if="!isLoading" type="button" @click.prevent="checkAccessId"
                                style="font-weight: bold; width: 150px;" class="btn btn-primary">Check Access ID</button>
                            <button type="button" v-else disabled style="font-weight: bold; width: 150px;"
                                class="btn btn-primary pb-0">
                                <Loading />
                            </button>
                        </div>
                    </div>
                    <div v-else>
                        <div class="row mb-2">
                            <div class="col-lg-12 mb-3">
                                <label for="firstName" style="font-weight: bold;" class="form-label">First Name:</label>
                                <input disabled type="text" v-model="auth.firstName" class="form-control" id="firstName">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.firstName">{{
                                    errors.firstName[0] }}</small>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="middleName" style="font-weight: bold;" class="form-label">Middle Name:</label>
                                <input disabled type="text" v-model="auth.middleName" class="form-control" id="middleName">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.middleName">{{
                                    errors.middleName[0] }}</small>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="lastName" style="font-weight: bold;" class="form-label">Last Name:</label>
                                <input disabled type="text" v-model="auth.lastName" class="form-control" id="lastName">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.lastName">{{
                                    errors.lastName[0] }}</small>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="lastName" style="font-weight: bold;" class="form-label">Gender:</label>
                                <select v-model="auth.gender" class="form-select">
                                    <option :value="null" selected disabled>Select Gender:</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                <small class="text-danger font-weight-bold" v-if="errors && errors.gender">{{
                                    errors.gender[0] }}</small>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="email" style="font-weight: bold;" class="form-label">Email Address:</label>
                                <input disabled type="email" v-model="auth.email" class="form-control" id="email">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.email">{{ errors.email[0]
                                }}</small>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="username" style="font-weight: bold;" class="form-label">Username:</label>
                                <input type="text" v-model="auth.username" class="form-control" id="username">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.username">{{
                                    errors.username[0] }}</small>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="password" style="font-weight: bold;" class="form-label">Password:</label>
                                <input type="password" v-model="auth.password" class="form-control" id="password">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.password">{{
                                    errors.password[0] }}</small>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="confirmPassword" style="font-weight: bold;" class="form-label">Confirm
                                    Password:</label>
                                <input type="password" v-model="auth.password_confirmation" class="form-control"
                                    id="confirmPassword">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.password_confirmation">{{
                                    errors.password_confirmation[0] }}</small>
                            </div>
                        </div>
                        <div class="text-center mb-3">
                            <button v-if="!isLoading" type="button" @click.prevent="register"
                                style="font-weight: bold; width: 120px;" class="btn btn-primary">Register</button>
                            <button type="button" v-else disabled style="font-weight: bold; width: 120px;"
                                class="btn btn-primary pb-0">
                                <Loading />
                            </button>
                        </div>
                    </div>
                    <div class="text-center">
                        <span style="font-weight: bold">Already have an account? <a style="font-weight: bold"
                                class="cursor-pointer" @click="redirectStudentLogin">Login</a></span>
                    </div>
                </form>
            </div>
        </div>
        <footer-component class="position-absolute bottom-0 start-50 translate-middle-x w-100"></footer-component>
    </body>
</template>

<script>
import swal from 'sweetalert2'
import Loading from '../../loading/Loading.vue'
export default {
    name: "Register",
    components: {
        Loading
    },
    data() {
        return {
            auth: {
                firstName: "",
                middleName: "",
                lastName: "",
                email: "",
                password: "",
                userFlag: 'teacher',
                gender: null,
                password_confirmation: "",
                username: "",
                teacherId: null
            },
            canAccess: false,
            accessId: '',
            isLoading: false,
            processing: false,
            errors: []
        };
    },
    methods: {
        redirectStudentLogin() {
            this.$router.push({ path: '/' })
        },
        async register() {
            this.errors = []
            this.isLoading = true
            await axios.post('api/register', this.auth)
                .then(response => {
                    swal.fire({
                        title: 'Success',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonText: 'Proceed to Login',
                    }).then((result) => {
                        if (result.value) {
                            location.href = '/'
                        }
                    })
                }).catch((error) => {
                    this.errors = error.response.data.errors
                }).finally(() => {
                    this.isLoading = false
                })
        },
        async checkAccessId() {
            this.errors = []
            this.isLoading = true
            await axios.get(`api/check-access-id?accessId=${this.accessId}`)
                .then(({ data }) => {
                    this.auth.firstName = data.first_name;
                    this.auth.middleName = data.middle_name;
                    this.auth.lastName = data.last_name;
                    this.auth.teacherId = data.id;
                    this.auth.email = data.email
                    this.canAccess = true
                }).catch((error) => {
                    this.errors = error.response.data.errors
                }).finally(() => {
                    this.isLoading = false
                })
        }
    },
};
</script>

<style scoped>
body {
    background-image: url("/images/sky-background.jpg");
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
    overflow-x: hidden
}

.logo {
    box-shadow: rgba(135, 206, 250, 0.4) 5px 5px, rgba(135, 206, 250, 0.3) 10px 10px, rgba(135, 206, 250, 0.2) 15px 15px, rgba(135, 206, 250, 0.1) 20px 20px, rgba(135, 206, 250, 0.05) 25px 25px;
}
.card {
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
    width: 500px !important;
    padding-bottom: 20px;
}

.card-body {
    height: 500px;
    overflow-x: hidden;
    overflow-y: scroll;
}

/* width */
::-webkit-scrollbar {
    width: 1px;
}

/* Track */
::-webkit-scrollbar-track {
    background: #f1f1f1;
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: #888;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: #007bff;
}

.cursor-pointer {
    cursor: pointer !important;
}

.title-header {
    font-family: 'Bangers', cursive;
    font-size: 30px;
}
</style>
