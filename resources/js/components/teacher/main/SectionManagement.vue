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
                            <button class="btn btn-success rounded-0"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#addSectionModal">
                                Add Section
                            </button>
                        </div>
                        <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Number</th>
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
                                <td>{{ section.name }}</td>
                                <td>{{ section.status }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm rounded-0">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-primary btn-sm rounded-0">
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
                const response = await axios.get('api/sections/add', this.sectionsToAdd);
                if (response.status == 200) {
                    swal.fire('Success', response.data.message, 'success');
                    this.getSections();
                    this.sectionsToAdd.name = null;
                }
            } catch (error) {
                console.log(error)
            }
        },
        async updateSection() {
            try {
                const response = await axios.get('api/sections/update/' + this.selectedSectionId, this.newSectionDetails);
                if (response.status == 200) {
                    swal.fire('Success', response.data.message, 'success');
                    this.getSections();
                    this.newSectionDetails.name = null;
                    this.newSectionDetails.status = null;
                    this.selectedSectionId = null;
                }
            } catch (error) {
                console.log(error)
            }
        },
        async deleteSection() {
            try {
                const response = await axios.get('api/sections/delete/' + this.selectedSectionId);
                if (response.status == 200) {
                    swal.fire('Success', response.data.message, 'success');
                    this.getSections();
                    this.selectedSectionId = null;
                }
            } catch (error) {
                console.log(error)
            }
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
</style>
