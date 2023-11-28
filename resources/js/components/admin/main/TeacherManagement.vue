<template>
    <div class="body position-relative">
        <div class="d-flex">
            <admin-sidebar-component @collapse="onCollapse" />
            <div :class="collapsed ? 'collapsed-menu' : 'not-collapsed-menu'" class="main-content mx-5 w-100 h-100 mt-5">
                <button class="btn btn-secondary mb-3 rounded-0" data-bs-toggle="modal"
                    data-bs-target="#addTeacherModal">Add
                    Teacher</button>
                <div class="d-flex justify-content-center">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Access ID</th>
                                <th scope="col">First Name</th>
                                <th scope="col">Middle Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="teachers.length !== 0">
                            <tr v-for="teacher in teachers" :key="teacher.id">
                                <td>{{ teacher.access_id }}</td>
                                <td>{{ teacher.first_name }}</td>
                                <td>{{ teacher.middle_name }}</td>
                                <td>{{ teacher.last_name }}</td>
                                <td>
                                    <p class="fw-bold p-2">{{ teacher.status ? (teacher.status == 'active' ? "ACTIVE" : "INACTIVE") : "NOT YET REGISTERED" }}</p>
                                </td>
                                <td class="d-flex flex-column">
                                    <button @click="viewStudents(teacher.id)" class="btn btn-secondary rounded-0 mb-3">
                                        <span>View Students </span><i class="fas fa-external-link-alt"></i>
                                    </button>
                                    <button v-if="teacher.status" @click="toggleConfirmationModal(teacher)" :class="teacher.status == 'active' ? 'btn-danger' : 'btn-success'" class="btn rounded-0 fw-bold">
                                        <span>{{ teacher.status == 'active' ? "Deactivate " : "Activate " }} </span>
                                        <i :class="teacher.status == 'active' ? 'fa-toggle-off' : 'fa-toggle-on'" class="fas"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="7" class="text-center">No records found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="teachers.length !== 0" class="d-flex justify-content-around">
                    <button :disabled="teachers.length < 11" class="btn btn-secondary rounded-0">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button :disabled="teachers.length < 11" class="btn btn-secondary rounded-0">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="addTeacherModal" data-bs-backdrop="static" tabindex="-1"
            aria-labelledby="addTeacherModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <img width="70" src="/images/main-logo.png" style="margin-right: 10px; border-radius: 50%;" class="logo" alt="Hopeminded Logo">
                        <h5 class="modal-title" id="addTeacherModalLabel">Add Teacher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="accessId" class="form-label">Teacher Access ID</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">HM-</span>
                                    <input v-model="auth.accessId" disabled type="text" class="form-control" id="accessId"
                                        aria-describedby="accessId">
                                </div>
                                <small class="text-danger font-weight-bold" v-if="errors && errors.accessId">{{
                                    errors.accessId[0] }}</small>
                                <div class="access-id-button mt-3">
                                    <button class="btn btn-success rounded-0" type="button"
                                        @click="generateAccessID">Generate Teacher Access ID</button>
                                </div>
                            </div>
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
                                <label for="email" class="form-label">Email Address</label>
                                <input v-model="auth.email" type="email" class="form-control" id="email">
                                <small class="text-danger font-weight-bold" v-if="errors && errors.email">{{
                                    errors.email[0] }}</small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button v-if="!isLoading" type="button" @click.prevent="addTeacher"
                            style="font-weight: bold; width: 120px;" class="btn btn-primary">Add Teacher</button>
                        <button type="button" v-else disabled style="font-weight: bold; width: 120px;"
                            class="btn btn-primary pb-0">
                            <Loading />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Teacher Confirmation Modal -->
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
                        <button v-if="!isLoading" type="button" @click.prevent="updateTeacherStatus(statusPrompt.user_id)"
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
export default {
    data() {
        return {
            collapsed: false,
            isLoading: false,
            teachers: [],
            errors: [],
            auth: {
                accessId: null,
                firstName: null,
                middleName: null,
                lastName: null,
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
        this.getTeachers()
    },
    methods: {
        onCollapse(isCollapsed) {
            if (isCollapsed) {
                this.collapsed = true
            } else {
                this.collapsed = false
            }
        },
        async getTeachers() {
            await axios.get('/api/users/teachers/get')
                .then(response => {
                    this.teachers = response.data
                })
                .catch(error => {
                    console.log(error)
                })

        },
        async addTeacher() {
            this.errors = []
            this.isLoading = true
            await axios.post('/api/users/teachers/add', this.auth)
                .then(response => {
                    $('#addTeacherModal').modal('hide')
                    this.getTeachers()
                    this.auth.accessId = null
                    this.auth.firstName = null
                    this.auth.middleName = null
                    this.auth.lastName = null
                    swal.fire('Success', response.data.message, 'success')
                })
                .catch(error => {
                    this.errors = error.response.data.errors
                })
                .finally(() => {
                    this.isLoading = false
                })
        },
        viewStudents(teacherId) {
            this.$router.push({
                path: `/teacher-management/${teacherId}`
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
        toggleConfirmationModal(teacher) {
            this.statusPrompt.status = teacher.status
            this.statusPrompt.user_id = teacher.user_id
            $('#updateStatusConfirmation').modal('show')
        },
        async updateTeacherStatus(userId) {
            try {
                this.isLoading = true
                const response = await axios.put(`/api/status/update/user/${userId}`)

                if (response.status == 200 || response.status == 201) {
                    $('#updateStatusConfirmation').modal('hide')
                    swal.fire('Success', response.data.message, 'success')
                    this.getTeachers();
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false
            }
        },
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
