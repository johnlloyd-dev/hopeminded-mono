<template>
    <div class="body position-relative">
        <div class="d-flex">
            <div class="main-content mx-5 w-100 h-100">
                <div class="my-3 d-flex d-flex justify-content-between align-items-center">
                    <button @click="navigate()" class="btn-secondary btn rounded-0">
                        Back
                    </button>
                </div>
                <div class="section-row mb-3">
                    <div class="card card-body w-25 rounded-0 border border-0">
                        <h4 class="fw-bold mb-0 text-black">Section: Test</h4>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <button type="button" @click="getStudentsNotInSection()" data-bs-toggle="modal" data-bs-target="#addStudentModal" class="btn btn-success rounded-0">Add Student</button>
                </div>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th style="width: 25%" scope="col">Email</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody v-if="studentsOfSectionLoading">
                        <tr>
                            <th colspan="4" class="text-center">Loading...</th>
                        </tr>
                    </tbody>
                    <tbody v-else-if="!studentsOfSectionLoading && studentsOfSection.length !== 0">
                        <tr v-for="student in studentsOfSection" :key="student.id">
                            <th scope="row">{{ combineName(student) }}</th>
                            <td class="fw-bold">{{ student.email }}</td>
                            <td>
                                <button @click="removeStudent(student.id)" type="button" class="btn btn-danger btn-sm rounded-0 me-3">
                                    Remove
                                </button>
                                <button @click="viewReport(student.id)" type="button" class="btn btn-secondary btn-sm rounded-0 me-3">
                                    Reports
                                </button>
                                <button @click="toggleConfirmationModal(student)" type="button" :class="student.status == 'active' ? 'btn-danger' : 'btn-success'" class="btn btn-sm rounded-0 me-3">
                                    <span>{{ student.status == 'active' ? "Deactivate " : "Activate " }} </span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else-if="!studentsOfSectionLoading && studentsOfSection.length === 0">
                        <tr>
                            <th colspan="3" class="text-center">No records found</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">Add Students To This Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <template v-if="!studentsNotInSectionLoading">
                            <template v-if="studentsNotInSection.length">
                                <div v-for="student in studentsNotInSection" :key="student.id" class="form-check">
                                    <input v-model="selectedStudentsIds" class="form-check-input" type="checkbox" :value="student.id" id="flexCheckDefault">
                                    <label class="form-check-label fw-bold" for="flexCheckDefault">
                                        {{ combineName(student) }}
                                    </label>
                                </div>
                            </template>
                            <template v-else>
                                <p class="text-center fw-bold">No records found</p>
                            </template>
                        </template>
                        <template v-else>
                            <p class="text-center fw-bold">Loading...</p>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-0" data-bs-dismiss="modal">Close</button>
                        <button @click="addStudentsToSection()" :disabled="!selectedStudentsIds.length" type="button" class="btn btn-primary rounded-0">Add Students To Section</button>
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
import swal from 'sweetalert2'
import Loading from '../../loading/Loading.vue'
import AlphabetReports from '../reports/AlphabetReports.vue'

export default {
    components: {
        AlphabetReports,
        Loading
    },
    data() {
        return {
            studentsOfSectionLoading: false,
            studentsNotInSectionLoading: false,
            studentsOfSection: [],
            studentsNotInSection: [],
            selectedStudentsIds: [],
            statusPrompt: {},
        }
    },
    created() {
        this.getStudentsOfSection()
    },
    methods: {
        viewReport(studentId) {
            this.$router.push({
                path: `/student-quiz-report/${studentId}`
            });
        },
        async getStudentsOfSection() {
            try {
                this.studentsOfSectionLoading = true
                const { data } = await axios.get('/api/section/students', {
                    params: {
                        sectionId: this.$route.params.sectionId
                    }
                });

                this.studentsOfSection = data;
            } catch (error) {
                console.log(error)
            } finally {
                this.studentsOfSectionLoading = false
            }
        },
        async getStudentsNotInSection() {
            try {
                this.studentsNotInSectionLoading = true
                const { data } = await axios.get('/api/section/students/not-in-section', {
                    params: {
                        sectionId: this.$route.params.sectionId
                    }
                });

                this.studentsNotInSection = data;
            } catch (error) {
                console.log(error)
            } finally {
                this.studentsNotInSectionLoading = false
            }
        },
        async addStudentsToSection() {
            try {
                const response = await axios.post('/api/section/students', {
                    selectedStudents: JSON.stringify(this.selectedStudentsIds),
                    sectionId: this.$route.params.sectionId
                });

                if ([200, 201].includes(response.status)) {
                    swal.fire('Success', response.data.message, 'success')
                    $('#addStudentModal').modal('hide')
                    this.getStudentsOfSection()
                    this.selectedStudentsIds = []
                }
            } catch (error) {
                console.log(error)
            }
        },
        async removeStudent(studentId) {
            swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, remove it!"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const { data, status } = await axios.delete('/api/section/student/' + studentId);

                        if ([200, 201].includes(status)) {
                            swal.fire({
                                title: "Deleted!",
                                text: data.message,
                                icon: "success"
                            });
                            this.getStudentsOfSection()
                        }
                    } catch (error) {
                        console.log(error)
                    }
                }
            });
        },
        combineName(student) {
            return `${this.upperCaseFirst(student.last_name)}, ${this.upperCaseFirst(student.first_name)} ${student.middle_name ? this.upperCaseFirst(student.middle_name.charAt(0)) + '.' : null}`
        },
        upperCaseFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },
        navigate() {
            this.$router.push('/section-management')
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
                    this.getStudentsOfSection()
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false
            }
        },
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

.card {
    background: rgb(174,209,238);
    background: radial-gradient(circle, rgba(174,209,238,1) 0%, rgba(148,233,226,1) 100%);
}
</style>
