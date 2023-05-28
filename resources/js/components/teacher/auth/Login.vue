<template>
    <body class="position-relative">
        <button @click="$router.push('/')" class="btn btn-secondary rounded-0 position-absolute top-0 start-0">
            Back to Home Page
        </button>
        <div class="card px-5 rounded-0 w-50">
            <h4 class="text-center mb-2 font-weight-bold title-header mt-3">Teacher Login</h4>
            <div class="card-body">
                <form>
                    <div class="row mb-2">
                        <div class="col-lg-12 mb-3">
                            <label for="username" class="form-label" style="font-weight: bold">Username:</label>
                            <input type="text" v-model="auth.username" class="form-control" id="username">
                            <small class="text-danger font-weight-bold" v-if="errors && errors.username">{{
                                errors.username[0] }}</small>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label for="password" class="form-label" style="font-weight: bold">Password:</label>
                            <input type="password" v-model="auth.password" class="form-control" id="password">
                            <small class="text-danger font-weight-bold" v-if="errors && errors.password">{{
                                errors.password[0] }}</small>
                        </div>
                    </div>
                    <div class="text-center mb-3">
                        <button @click.prevent="login" v-if="!isLoading" style="font-weight: bold; width: 120px;"
                            type="button" class="btn btn-primary">Login</button>
                        <button v-else disabled style="font-weight: bold; width: 120px;" type="button"
                            class="btn btn-primary pb-0">
                            <Loading />
                        </button>
                    </div>
                    <!-- <div class="text-center">
                        <span style="font-weight: bold">Don't have an account yet? <a class="cursor-pointer"
                                style="font-weight: bold" @click="redirectRegister">Register</a></span>
                    </div> -->
                </form>
            </div>
        </div>
        <footer-component class="position-absolute bottom-0 start-50 translate-middle-x w-100"></footer-component>
    </body>
</template>

<script>
import Loading from '../../loading/Loading.vue'
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
                userFlag: 'teacher'
            },
            processing: false,
            isLoading: false,
            errors: []
        }
    },
    methods: {
        redirectRegister() {
            this.$router.push({ path: '/teacher-register' })
        },
        async login() {
            this.isLoading = true
            this.errors = []
            await axios.post('api/login', this.auth)
                .then(({ data }) => {
                    localStorage.setItem('token', data.data.token)
                    localStorage.setItem('fullname', data.data.fullname)
                    this.$router.push({ path: '/student-management' })
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
</style>