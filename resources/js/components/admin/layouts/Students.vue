<template>
    <div class="body position-relative">
        <div class="d-flex">
            <div class="main-content mx-5 w-100 h-100">
                <div class="my-3">
                    <button @click="navigate()" class="btn-secondary btn rounded-0">
                        Back
                    </button>
                </div>
                <h3 class="fw-bold mt-5 mb-2">Students</h3>
                <div class="d-flex justify-content-center">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Student Access ID</th>
                                <th scope="col">First Name</th>
                                <th scope="col">Middle Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody v-if="isProcessing">
                            <tr>
                                <th colspan="6" class="text-center">Loading...</th>
                            </tr>
                        </tbody>
                        <tbody v-else-if="!isProcessing && students.length !== 0">
                            <tr v-for="student in students" :key="student.id">
                                <th scope="row">{{ student.access_id }}</th>
                                <td>{{ student.first_name }}</td>
                                <td>{{ student.middle_name }}</td>
                                <td>{{ student.last_name }}</td>
                                <td>
                                    <span v-if="student.is_registered == 0">Pending</span>
                                    <span v-else>Registered</span>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else-if="!isProcessing && students.length === 0">
                            <tr>
                                <th colspan="5" class="text-center">No records found</th>
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
    </div>
</template>

<script>
import swal from 'sweetalert2'
import Loading from '../../loading/Loading.vue'
export default {
    components: {
        Loading
    },
    data() {
        return {
            students: [],
            isProcessing: false,
        }
    },
    created() {
        this.teacherId = this.$route.params.teacherId
        this.getStudents()
    },
    methods: {
        async getStudents() {
            this.isProcessing = true
            try {
                const response = await axios.get(`/api/students-of-teacher/${this.teacherId}`)
                this.students = response.data
            } catch (error) {
                console.log(error)
            } finally {
                this.isProcessing = false
            }
        },
        navigate() {
            this.$router.push('/teacher-management')
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
</style>
