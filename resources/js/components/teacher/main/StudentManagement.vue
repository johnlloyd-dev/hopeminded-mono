<template>
    <div class="body">
        <div class="d-flex">
            <teacher-sidebar-component @collapse="onCollapse" />
            <div :class="collapsed ? 'collapsed-menu' : 'not-collapsed-menu'" class="main-content mx-5 w-100 h-100 mt-5">
                <button class="btn btn-secondary mb-3 rounded-0" data-bs-toggle="modal"
                    data-bs-target="#addStudentModal">Add
                    Student</button>
                <div class="d-flex justify-content-center">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">First Name</th>
                                <th scope="col">Middle Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Username</th>
                                <th scope="col">Password</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody v-if="isLoading">
                            <tr>
                                <th colspan="6" class="text-center">Loading...</th>
                            </tr>
                        </tbody>
                        <tbody v-else-if="!isLoading && students.length !== 0">
                            <tr v-for="student in students" :key="student.id">
                                <td>{{ student.first_name }}</td>
                                <td>{{ student.middle_name }}</td>
                                <td>{{ student.last_name }}</td>
                                <td>{{ student.username }}</td>
                                <td>{{ student.unhashed }}</td>
                                <td class="d-flex justify-content-center">
                                    <button @click="viewReport(student.id)" class="btn btn-success rounded-0">
                                        <span>Student Report </span><i class="fas fa-external-link-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else-if="!isLoading && students.length === 0">
                            <tr>
                                <th colspan="6" class="text-center">No records found</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="students.length !== 0" class="d-flex justify-content-around">
                    <button :disabled="students.length <= 10" class="btn btn-secondary rounded-0">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button :disabled="students.length <= 10" class="btn btn-secondary rounded-0">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="addStudentModal" data-bs-backdrop="static" tabindex="-1"
            aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <img width="70" src="/images/main-logo.png" style="margin-right: 10px; border-radius: 50%"
                            class="logo" alt="Hopeminded Logo">
                        <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="firstname" class="form-label">First Name</label>
                                <input v-model="auth.firstName" type="text" class="form-control" id="firstname"
                                    aria-describedby="firstname">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.firstName">{{
                                    errors.firstName[0] }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="middlename" class="form-label">Middle Name</label>
                                <input v-model="auth.middleName" type="text" class="form-control" id="middlename">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.middleName">{{
                                    errors.middleName[0] }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input v-model="auth.lastName" type="text" class="form-control" id="lastname">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.lastName">{{
                                    errors.lastName[0] }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Gender:</label>
                                <select v-model="auth.gender" class="form-select">
                                    <option :value="null" selected disabled>Select Gender:</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                <small class="text-danger font-weight-bold" v-if="errors && errors.gender">{{
                                    errors.gender[0] }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input v-model="auth.email" type="email" class="form-control" id="email">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.email">{{
                                    errors.email[0] }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input v-model="auth.username" type="text" class="form-control" id="username">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.username">{{
                                    errors.username[0] }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input v-model="auth.password" type="text" class="form-control" id="password">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.password">{{
                                    errors.password[0] }}</small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button v-if="!isLoading" type="button" @click.prevent="addStudent"
                            style="font-weight: bold; width: 120px;" class="btn btn-primary">Add Student</button>
                        <button type="button" v-else disabled style="font-weight: bold; width: 120px;"
                            class="btn btn-primary pb-0">
                            <Loading />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Loading from '../../loading/Loading.vue'
import swal from 'sweetalert2'
import { Tooltip } from 'bootstrap'
export default {
    data() {
        return {
            collapsed: false,
            isLoading: false,
            students: [],
            errors: [],
            auth: {
                firstName: null,
                middleName: null,
                lastName: null,
                gender: null,
                email: null,
                username: null,
                password: null
            }
        }
    },
    components: {
        Loading
    },
    mounted() {
        this.getStudents()
        new Tooltip(document.body, {
            selector: "[data-bs-toggle='tooltip']",
        })
    },
    methods: {
        onCollapse(isCollapsed) {
            if (isCollapsed) {
                this.collapsed = true
            } else {
                this.collapsed = false
            }
        },
        async getStudents() {
            this.isLoading = true
            await axios.get('/api/users/students/get')
                .then(response => {
                    this.students = response.data
                })
                .catch(error => {
                    console.log(error)
                }).finally(() => {
                    this.isLoading = false
                })

        },
        viewReport(studentId) {
            this.$router.push({
                path: `/student-quiz-report/${studentId}`
            });
        },
        generateAccessID() {
            var characters = '0123456789';
            var accessID = '';

            for (var i = 0; i < 7; i++) {
                var randomIndex = Math.floor(Math.random() * characters.length);
                accessID += characters.charAt(randomIndex);
            }

            this.auth.accessId = accessID;

        },
        async addStudent() {
            this.errors = []
            this.isLoading = true
            await axios.post('/api/users/students/add', this.auth)
                .then(response => {
                    $('#addStudentModal').modal('hide')
                    this.getStudents()
                    this.auth.email = null
                    this.auth.firstName = null
                    this.auth.middleName = null
                    this.auth.lastName = null
                    this.auth.gender = null
                    this.auth.username = null
                    this.auth.password = null
                    swal.fire('Success', response.data.message, 'success')
                })
                .catch(error => {
                    this.errors = error.response.data.errors
                })
                .finally(() => {
                    this.isLoading = false
                })
        }
    }
}
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

.card {
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
}

.card-title {
    font-weight: bold;
}

.card-sl {
    border-radius: 8px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    width: 250px;
    height: 270px;
}

.card-image {
    display: flex;
    justify-content: center;
}

.card-image img {
    width: 230px;
    height: 230px;
    border-radius: 8px 8px 0px 0;
}

.card-action {
    position: relative;
    float: right;
    margin-top: -25px;
    margin-right: 20px;
    z-index: 2;
    color: #E26D5C;
    background: #fff;
    border-radius: 50%;
    padding: 15px;
    font-size: 15px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 2px 0 rgba(0, 0, 0, 0.19);
}

.card-action:hover {
    color: #fff;
    background: #E26D5C;
    animation: pulse 1.5s infinite;
    -webkit-animation: pulse 1.5s infinite;
}

.card-heading {
    font-size: 18px;
    font-weight: bold;
    background: #fff;
    padding: 10px 15px;
    border-radius: 8px;
}

.card-text {
    padding: 10px 15px;
    background: #fff;
    font-size: 14px;
    color: #636262;
}

.card-button {
    display: flex;
    justify-content: center;
    padding: 10px 0;
    width: 100%;
    background-color: #1F487E;
    color: #fff;
    border-radius: 0 0 8px 8px;
}

.card-button:hover {
    text-decoration: none;
    background-color: #1D3461;
    color: #fff;

}

@keyframes pulse {
    0% {
        -moz-transform: scale(0.9);
        -ms-transform: scale(0.9);
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
    }

    70% {
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        -webkit-transform: scale(1);
        transform: scale(1);
        box-shadow: 0 0 0 50px rgba(90, 153, 212, 0);
    }

    100% {
        -moz-transform: scale(0.9);
        -ms-transform: scale(0.9);
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
        box-shadow: 0 0 0 0 rgba(90, 153, 212, 0);
    }
}

@-webkit-keyframes pulse {
    0% {
        -moz-transform: scale(0.9);
        -ms-transform: scale(0.9);
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
    }

    70% {
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        -webkit-transform: scale(1);
        transform: scale(1);
        box-shadow: 0 0 0 50px rgba(90, 153, 212, 0);
    }

    100% {
        -moz-transform: scale(0.9);
        -ms-transform: scale(0.9);
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
        box-shadow: 0 0 0 0 rgba(90, 153, 212, 0);
    }
}
</style>
