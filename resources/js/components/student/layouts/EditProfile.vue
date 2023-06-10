<template>
    <div class="body position-relative">
        <div class="d-flex">
            <div class="main-content mx-5 w-100 h-100">
                <button type="button" @click="navigate" class="btn btn-secondary mt-5 rounded-0">
                    Back
                </button>
                <div class="edit-profile">
                    <div class="registration-form mb-5">
                        <form>
                            <div class="form-icon">
                                <span><img width="100" src="/images/logo.png" alt="Main Logo"></span>
                            </div>
                            <h3 class="fw-bold text-center mb-3">Your Profile</h3>
                            <div class="form-group mb-3">
                                <label for="firstName" class="fw-bold">First Name:</label>
                                <input v-model="profile.first_name" type="text" class="form-control item" id="firstName">
                                <small class="text-danger" v-if="errors && errors.first_name">{{ errors.first_name[0] }}</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="middleName" class="fw-bold">Middle Name:</label>
                                <input v-model="profile.middle_name" type="text" class="form-control item" id="middleName">
                                <small class="text-danger" v-if="errors && errors.middle_name">{{ errors.middle_name[0] }}</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="lastName" class="fw-bold">Last Name:</label>
                                <input v-model="profile.last_name" type="text" class="form-control item" id="lastName">
                                <small class="text-danger" v-if="errors && errors.last_name">{{ errors.last_name[0] }}</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="gender" class="fw-bold">Gender:</label>
                                <select v-model="profile.gender" class="form-select item" id="gender" aria-label="Default select example">
                                    <option :value="null" selected>Select Gender:</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="others">Others</option>
                                </select>
                                <small class="text-danger" v-if="errors && errors.gender">{{ errors.gender[0] }}</small>
                            </div>
                            <hr>
                            <h3 class="fw-bold text-center mb-3">Auth Credentials</h3>
                            <div class="form-group mb-3">
                                <label for="email" class="fw-bold">Email Address:</label>
                                <input v-model="profile.email" type="email" class="form-control item" id="email">
                                <small class="text-danger" v-if="errors && errors.email">{{ errors.email[0] }}</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="username" class="fw-bold">Username:</label>
                                <input v-model="profile.username" type="text" class="form-control item" id="username">
                                <small class="text-danger" v-if="errors && errors.username">{{ errors.username[0] }}</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password" class="fw-bold">New Password (Optional):</label>
                                <input v-model="profile.password" type="password" class="form-control item" id="password">
                                <small class="text-danger" v-if="errors && errors.password">{{ errors.password[0] }}</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="confirmPassword" class="fw-bold">Confirm New Password (Optional):</label>
                                <input v-model="profile.password_confirmation" type="password" class="form-control item" id="confirmPassword">
                                <small class="text-danger" v-if="errors && errors.password_confirmation">{{ errors.password_confirmation[0] }}</small>
                            </div>
                            <div class="form-group mb-3">
                                <button v-if="!isProcessing" type="button" style="width: 170px" @click="updateProfile" class="btn btn-block create-account">Update Profile</button>
                                <button v-else type="button" style="width: 170px" disabled class="btn btn-block create-account">
                                    <Loading />
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Loading from '../../loading/Loading.vue'
import swal from 'sweetalert2'
export default {
    components: {
        Loading
    },
    data() {
        return {
            collapsed: false,
            isProcessing: false,
            data: [],
            errors: [],
            profile: {
                first_name: null,
                middle_name: null,
                last_name: null,
                gender: null,
                email: null,
                username: null,
                password: null,
                password_confirmation: null
            }
        };
    },
    created() {
        this.getProfile()
    },
    computed: {

    },
    methods: {
        navigate() {
            this.$router.go(-1)
        },
        async getProfile() {
            try {
                this.isProcessing = true
                const response = await axios.get(`/api/profile/get`)
                const data = response.data
                this.profile.first_name = data.first_name
                this.profile.middle_name = data.middle_name
                this.profile.last_name = data.last_name
                this.profile.gender = data.gender
                this.profile.email = data.email
                this.profile.username = data.username
            } catch (error) {
                console.log(error)
            } finally {
                this.isProcessing = false
            }
        },
        async updateProfile() {
            try {
                this.errors = []
                this.isProcessing = true
                const response = await axios.post(`/api/profile/update`, this.profile)
                if(response.status === 200) {
                    swal.fire('Sucess', response.data.message, 'success')
                    this.getProfile()
                }
            } catch (error) {
                this.errors = error.response.data.errors
            } finally {
                this.isProcessing = false
            }
        }
    },
};
</script>

<style scoped>
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

.registration-form form {
    background-color: #fff;
    max-width: 600px;
    margin: auto;
    padding: 50px 70px;
    border-top-left-radius: 30px;
    border-top-right-radius: 30px;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.075);
}

.registration-form .form-icon {
    width: 100px;
    height: 100px;
    margin: auto;
    margin-bottom: 50px;
    line-height: 100px;
}

.registration-form .item {
    border-radius: 20px;
    padding: 10px 20px;
}

.registration-form .create-account {
    border-radius: 30px;
    padding: 10px 20px;
    font-size: 18px;
    font-weight: bold;
    background-color: #5791ff;
    border: none;
    color: white;
    margin-top: 20px;
}

@media (max-width: 576px) {
    .registration-form form {
        padding: 50px 20px;
    }

    .registration-form .form-icon {
        width: 70px;
        height: 70px;
        font-size: 30px;
        line-height: 70px;
    }
}
</style>
