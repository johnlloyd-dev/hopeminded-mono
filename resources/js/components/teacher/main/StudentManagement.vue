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
                                <th scope="col">Student</th>
                                <th style="width: 25%" scope="col">Skill Test Report</th>
                                <th style="width: 25%" scope="col">Quiz Report</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody v-if="isLoading">
                            <tr>
                                <th colspan="6" class="text-center">Loading...</th>
                            </tr>
                        </tbody>
                        <tbody v-else-if="!isLoading && students.length !== 0">
                            <tr v-for="(student, index) in students" :key="student.id">
                                <td class="w-25">
                                    <span class="fw-bold">{{ `${student.last_name}, ${student.first_name}
                                                                            ${student.middle_name ?
                                            student.middle_name.charAt(0) + '.' : null}` }}</span>
                                    <button class="btn border-0" type="button" data-bs-toggle="collapse"
                                        :data-bs-target="`#personalInfoCollapse${index}`" aria-expanded="false"
                                        aria-controls="personalInfoCollapse">
                                        <i class="fas fa-external-link-alt"></i>
                                    </button>
                                    <div class="collapse" :id="`personalInfoCollapse${index}`">
                                        <ul class="list-group">
                                            <li class="list-group-item">First Name: <span class="fw-bold">{{
                                                student.first_name }}</span></li>
                                            <li class="list-group-item">Middle Name: <span class="fw-bold">{{
                                                student.middle_name }}</span></li>
                                            <li class="list-group-item">Last Name: <span class="fw-bold">{{
                                                student.last_name }}</span></li>
                                            <li class="list-group-item">Username: <span class="fw-bold">{{ student.username
                                            }}</span></li>
                                            <li class="list-group-item">Password: <span class="fw-bold">{{ student.unhashed
                                            }}</span></li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <button class="btn rounded-0 btn-secondary btn-sm fw-bold w-100" type="button"
                                            data-bs-toggle="collapse" :data-bs-target="`#alphabetLetters${index}`"
                                            aria-expanded="false" aria-controls="alphabetLetters">
                                            Alphabets/Letters
                                        </button>
                                        <div class="collapse fw-bold text-center bg-info bg-gradient" :id="`alphabetLetters${index}`">
                                            <span class="mb-0 me-2">Submitted: <span class="fw-bold rounded-5 p-1">{{
                                                getProgressPercentage(student, 'alphabet-letters') }}/26</span></span>
                                            <span class="vr"></span>
                                            <span class="mb-0 ms-2">Lacking: <span class="fw-bold rounded-5 p-1">{{ 26 -
                                                (getProgressPercentage(student, 'alphabet-letters'))}}</span></span>
                                            <div class="progress rounded-0 bg-white">
                                                <div style="min-width: 20px;" :class="getProgressPercentage(student, 'alphabet-letters') !== 0 ? 'progress-bar-striped progress-bar-animated' : 'bg-white text-dark'" class="progress-bar" role="progressbar" :style="`width: ${Math.round(((getProgressPercentage(student, 'alphabet-letters'))/26) * 100)}%;`" :aria-valuenow="Math.round(((getProgressPercentage(student, 'alphabet-letters'))/26) * 100)" aria-valuemin="0" aria-valuemax="100">{{ Math.round(((getProgressPercentage(student, 'alphabet-letters'))/26) * 100) }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn rounded-0 btn-secondary btn-sm mt-2 fw-bold w-100" type="button"
                                            data-bs-toggle="collapse" :data-bs-target="`#vowelConsonants${index}`"
                                            aria-expanded="false" aria-controls="vowelConsonants">
                                            Vowels/Consonants
                                        </button>
                                        <div class="collapse fw-bold text-center bg-warning bg-gradient"
                                            :id="`vowelConsonants${index}`">
                                            <span class="mb-0 me-2">Submitted: <span class="fw-bold rounded-5 p-1">{{
                                                getProgressPercentage(student, 'vowel-consonants') }}/26</span></span>
                                            <span class="vr"></span>
                                            <span class="mb-0 ms-2">Lacking: <span class="fw-bold rounded-5 p-1">{{ 26 -
                                                (getProgressPercentage(student, 'vowel-consonants'))}}</span></span>
                                            <div class="progress rounded-0 bg-white">
                                                <div style="min-width: 20px;" :class="getProgressPercentage(student, 'vowel-consonants') !== 0 ? 'progress-bar-striped progress-bar-animated' : 'bg-white text-dark'" class="progress-bar" role="progressbar" :style="`width: ${Math.round(((getProgressPercentage(student, 'vowel-consonants'))/26) * 100)}%;`" :aria-valuenow="Math.round(((getProgressPercentage(student, 'vowel-consonants'))/26) * 100)" aria-valuemin="0" aria-valuemax="100">{{ Math.round(((getProgressPercentage(student, 'vowel-consonants'))/26) * 100) }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn rounded-0 btn-secondary btn-sm mt-2 fw-bold w-100" type="button"
                                            data-bs-toggle="collapse" :data-bs-target="`#alphabetWords${index}`"
                                            aria-expanded="false" aria-controls="alphabetWords">
                                            Alphabets/Words
                                        </button>
                                        <div class="collapse fw-bold text-center bg-success bg-gradient text-white" :id="`alphabetWords${index}`">
                                            <span class="mb-0 me-2">Submitted: <span class="fw-bold rounded-5 p-1">{{
                                                getProgressPercentage(student, 'alphabet-words') }}/26</span></span>
                                            <span class="vr"></span>
                                            <span class="mb-0 ms-2">Lacking: <span class="fw-bold rounded-5 p-1">{{ 26 -
                                                (getProgressPercentage(student, 'alphabet-words'))}}</span></span>
                                                <div class="progress rounded-0 bg-white">
                                                    <div style="min-width: 20px;" :class="getProgressPercentage(student, 'alphabet-words') !== 0 ? 'progress-bar-striped progress-bar-animated' : 'bg-white text-dark'" class="progress-bar" role="progressbar" :style="`width: ${Math.round(((getProgressPercentage(student, 'alphabet-words'))/26) * 100)}%;`" :aria-valuenow="Math.round(((getProgressPercentage(student, 'alphabet-words'))/26) * 100)" aria-valuemin="0" aria-valuemax="100">{{ Math.round(((getProgressPercentage(student, 'alphabet-words'))/26) * 100) }}%</div>
                                                </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="w-25">
                                    <div>
                                        <button class="btn rounded-0 btn-secondary btn-sm fw-bold w-100" type="button"
                                            data-bs-toggle="collapse" :data-bs-target="`#memoryGameCollapse${index}`"
                                            aria-expanded="false" aria-controls="memoryGameCollapse">
                                            Memory Game
                                        </button>
                                        <div class="collapse fw-bold text-center bg-info"
                                            :id="`memoryGameCollapse${index}`">
                                            <span>Highest Score: {{ student.memory_game }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn rounded-0 btn-secondary btn-sm mt-2 fw-bold w-100" type="button"
                                            data-bs-toggle="collapse" :data-bs-target="`#typingBalloonCollapse${index}`"
                                            aria-expanded="false" aria-controls="typingBalloonCollapse">
                                            Typing Balloon
                                        </button>
                                        <div class="collapse fw-bold text-center bg-warning"
                                            :id="`typingBalloonCollapse${index}`">
                                            <span>Highest Score: {{ student.typing_balloon }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn rounded-0 btn-secondary btn-sm mt-2 fw-bold w-100" type="button"
                                            data-bs-toggle="collapse" :data-bs-target="`#hangmanGameCollapse${index}`"
                                            aria-expanded="false" aria-controls="hangmanGameCollapse">
                                            Hangman Game
                                        </button>
                                        <div class="collapse fw-bold text-center bg-white"
                                            :id="`hangmanGameCollapse${index}`">
                                            <span>Highest Score: {{ student.hangman_game }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="fw-bold p-2">{{ student.status == 'active' ? "ACTIVE" : "INACTIVE" }}</p>
                                </td>
                                <td class="d-flex flex-column">
                                    <button @click="viewReport(student.id)" class="btn btn-secondary fw-bold rounded-0 mb-3">
                                        <span>Reports </span><i class="fas fa-external-link-alt"></i>
                                    </button>
                                    <button @click="toggleConfirmationModal(student)" :class="student.status == 'active' ? 'btn-danger' : 'btn-success'" class="btn rounded-0 fw-bold">
                                        <span>{{ student.status == 'active' ? "Deactivate " : "Activate " }} </span>
                                        <i :class="student.status == 'active' ? 'fa-toggle-off' : 'fa-toggle-on'" class="fas"></i>
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

        <!-- Update Student Confirmation Modal -->
        <div class="modal fade" id="updateStatusConfirmation" data-bs-backdrop="static" tabindex="-1"
            aria-labelledby="updateStatusConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <img width="70" src="/images/main-logo.png" style="margin-right: 10px; border-radius: 50%"
                            class="logo" alt="Hopeminded Logo">
                        <h5 class="modal-title" id="updateStatusConfirmationModalLabel">Update Status Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fw-bold">Do you want to proceed in {{ statusPrompt.status == 'active' ? 'deactivating' : 'activating' }} this student's account?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button v-if="!isLoading" type="button" @click.prevent="updateStudentStatus(statusPrompt.user_id)"
                            style="font-weight: bold; width: 120px;" class="btn btn-primary">Proceed</button>
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
            },
            statusPrompt: {}
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
        toggleConfirmationModal(student) {
            this.statusPrompt.status = student.status
            this.statusPrompt.user_id = student.user_id
            $('#updateStatusConfirmation').modal('show')
        },
        async updateStudentStatus(userId) {
            try {
                this.isLoading = true
                const response = await axios.put(`/api/status/update/user/${userId}`)

                if (response.status == 200 || response.status == 201) {
                    $('#updateStatusConfirmation').modal('hide')
                    swal.fire('Success', response.data.message, 'success')
                    this.getStudents();
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false
            }
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
        },
        getProgressPercentage(student, key) {
            return student.skill_test && student.skill_test[key] ? Object.keys(student.skill_test[key]).length : 0
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
