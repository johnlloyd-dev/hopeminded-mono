<template>
    <div class="body">
        <div class="d-flex">
            <teacher-sidebar-component @collapse="onCollapse" />
            <div :class="collapsed ? 'collapsed-menu' : 'not-collapsed-menu'" class="main-content mx-5 w-100 h-100">
                <div class="d-flex justify-content-center">
                    <div class="card rounded-0 py-5 px-5" style="width: 100%;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold">Section Management </h4>
                        </div>
                        <div class="mb-2">
                            <button class="btn btn-primary rounded-0"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#addSectionModal">
                                Add Section
                            </button>
                        </div>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th style="width: 25%" scope="col">Name</th>
                                    <th style="width: 25%" scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody v-if="processing">
                                <tr>
                                    <th colspan="4" class="text-center">Loading...</th>
                                </tr>
                            </tbody>
                            <tbody v-else-if="!processing && sections.length !== 0">
                                <tr v-for="(section, index) in sections" :key="section.id">
                                    <th scope="row">{{ index + 1 }}</th>
                                    <td class="fw-bold">{{ section.name }}</td>
                                    <td>{{ section.status.charAt(0).toUpperCase() + section.status.slice(1) }}</td>
                                    <td>
                                        <button @click="viewSection(section.id)" type="button" class="btn btn-primary btn-sm rounded-0 me-3">
                                            <i class="fas fa-users"></i>
                                        </button>
                                        <button @click="handleSectionUpdate(section)" type="button" class="btn btn-success btn-sm rounded-0 me-3">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="deleteSection(section.id)" type="button" class="btn btn-danger btn-sm rounded-0">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else-if="!processing && sections.length === 0">
                                <tr>
                                    <th colspan="4" class="text-center">No records found</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Add Section Modal -->
                <div class="modal fade" id="addSectionModal" data-bs-backdrop="static" tabindex="-1"
                    aria-labelledby="addSectionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <img width="70" src="/images/main-logo.png" style="margin-right: 10px; border-radius: 50%"
                                    class="logo" alt="Hopeminded Logo">
                                <h5 class="modal-title" id="addSectionModalLabel">Add Section</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Name: </label>
                                    <input v-model="sectionsToAdd.name" type="text" class="form-control" required/>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" @click="addSection()"
                                    style="font-weight: bold; width: 120px;" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Update Section Modal -->
                <div class="modal fade" id="updateSectionModal" data-bs-backdrop="static" tabindex="-1"
                    aria-labelledby="updateSectionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <img width="70" src="/images/main-logo.png" style="margin-right: 10px; border-radius: 50%"
                                    class="logo" alt="Hopeminded Logo">
                                <h5 class="modal-title" id="updateSectionModalLabel">Update Section</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Name: </label>
                                    <input v-model="newSectionDetails.name" type="text" class="form-control" required/>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Status: </label>
                                    <select v-model="newSectionDetails.status" class="form-select" aria-label="Default select example">
                                        <option :value="null" disabled>Please select a status...</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" @click="updateSection()"
                                    style="font-weight: bold; width: 120px;" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Loading from '../../loading/Loading.vue';
import swal from 'sweetalert2';

export default {
    components: {
        Loading
    },
    data() {
        return {
            collapsed: false,
            processing: false,
            sections: [],
            sectionsToAdd: {
                name: null
            },
            newSectionDetails: {
                name: null,
                status: null
            },
            selectedSectionId: null
        }
    },
    mounted() {
        this.getSections();
    },
    methods: {
        onCollapse(isCollapsed) {
            if (isCollapsed) {
                this.collapsed = true
            } else {
                this.collapsed = false
            }
        },
        async getSections() {
            try {
                const response = await axios.get('api/sections/get');
                if (response.status == 200) {
                    this.sections = response.data
                }
            } catch (error) {
                console.log(error)
            }
        },
        async addSection() {
            try {
                const response = await axios.post('api/sections/add', this.sectionsToAdd);
                if (response.status == 200) {
                    swal.fire('Success', response.data.message, 'success');
                    $('#addSectionModal').modal('hide');
                    this.getSections();
                    this.sectionsToAdd.name = null;
                }
            } catch (error) {
                console.log(error)
            }
        },
        handleSectionUpdate(section) {
            this.selectedSectionId = section.id;
            this.newSectionDetails.name = section.name;
            this.newSectionDetails.status = section.status;
            $('#updateSectionModal').modal('show');
        },
        async updateSection() {
            try {
                const response = await axios.put('api/sections/update/' + this.selectedSectionId, this.newSectionDetails);
                if (response.status == 200) {
                    swal.fire('Success', response.data.message, 'success');
                    this.getSections();
                    this.newSectionDetails.name = null;
                    this.newSectionDetails.status = null;
                    this.selectedSectionId = null;
                    $('#updateSectionModal').modal('hide');
                }
            } catch (error) {
                console.log(error)
            }
        },
        async deleteSection(sectionId) {
            try {
                const response = await axios.delete('api/sections/delete/' + sectionId);
                if (response.status == 200) {
                    swal.fire('Success', response.data.message, 'success');
                    this.getSections();
                }
            } catch (error) {
                console.log(error)
            }
        },
        viewSection(sectionId) {
            this.$router.push('/section/' + sectionId)
        }
    },
}
</script>

<style scoped>
.body {
    background-image: url("/images/background.jpg");
    background-size: cover;
    background-repeat: repeat;
    background-attachment: fixed;
    color: #101c2a;
    padding: 0;
    margin: 0;
    height: 100vh;
    overflow-x: hidden;
}

.card {

    background-color: rgba(232, 223, 223, 0.1);
    border: none;
}
</style>
