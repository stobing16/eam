<template>
    <div class="asset-transaksi-page">
        <h1 class="page-title">Asset</h1>
        <!-- Button to trigger the modal to create a new employee -->
        <div class="d-flex mb-3 justify-content-end gap-2">
            <button class="btn btn-primary" @click="openCreateModal">Add New Asset</button>
            <!-- <button class="btn btn-primary" @click="openExcelCreateModal">Export Excel</button> -->
        </div>
        <div class="search-bar">
            <input type="text" class="form-control" v-model="search" @input="fetchProject"
                placeholder="Search project..." />
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Company</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Asset Code</th>
                        <th>Asset Name</th>
                        <th>Condition</th>
                        <th>Location</th>
                        <th>Checkout To</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(project, index) in projects" :key="project.RowId">
                        <td>{{ index + 1 + ((pagination.currentPage - 1) * pagination.rowsPerPage) }}</td>
                        <td>{{ project.ProjectCode }}</td>
                        <td>{{ project.ProjectName }}</td>
                        <td>{{ project.Status === 'A' ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" @click="openEditModal(project)">Edit</button>
                            <!-- <button class="btn btn-danger btn-sm" @click="deleteEmployee(location.RowId)">Delete</button> -->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <button class="btn btn-secondary" :disabled="pagination.currentPage === 1"
                @click="fetchProject(pagination.currentPage - 1)">
                Previous
            </button>
            <span>Page {{ pagination.currentPage }} of {{ pagination.totalPages }}</span>
            <button class="btn btn-secondary" :disabled="pagination.currentPage === pagination.totalPages"
                @click="fetchProject(pagination.currentPage + 1)">
                Next
            </button>
        </div>


        <div class="modal" tabindex="-1" :class="{ show: showModal }" style="display: block;" v-if="showModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Edit Project' : 'Add Project' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3" v-if="isEditing">
                            <label for="name">Project Code</label>
                            <input type="text" class="form-control" v-model="form.code" disabled />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Project Name</label>
                            <input type="text" class="form-control" v-model="form.name" />
                        </div>
                        <div class="form-group mb-3" v-if="isEditing">
                            <label for="status">Status</label>
                            {{ form.status }}
                            <select id="status" class="form-control" v-model="form.status">
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-primary"
                            @click="isEditing ? updateProject() : createProject()">
                            {{ isEditing ? 'Update' : 'Submit' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";

const assets = ref([])
const search = ref("")
const pagination = ref({
    currentPage: 1,
    rowsPerPage: 10,
    totalPages: 1,
})

const form = ref({
    name: ''
})

const showModal = ref(false)
const isEditing = ref(false)

onMounted(() => {
    fetchProject()
})

const fetchProject = async (page = 1) => {
    try {
        page = Number(page);

        const response = await axios.get("/api/transaksi/assets", {
            params: {
                per_page: pagination.value.rowsPerPage,
                current_page: page,
                search: search.value,
            },
        });

        const data = response.data;
        assets.value = data.data;
        // console.log(assets.value)

        pagination.value.currentPage = Number(data.current_page);
        pagination.value.rowsPerPage = data.per_page;
        pagination.value.totalPages = data.from;
    } catch (error) {
        console.error("Error fetching Assets:", error);
    }
}

const openCreateModal = () => {
    showModal.value = true
    isEditing.value = false
}

const closeModal = () => {
    showModal.value = false
    isEditing.value = false
    form.value = {
        name: ''
    }
}

const openEditModal = (item) => {
    console.log(item)
    form.value.RowId = item.RowId
    form.value.code = item.ProjectCode
    form.value.name = item.ProjectName
    form.value.status = item.Status

    showModal.value = true;
    isEditing.value = true;
}

const createProject = async () => {
    try {
        const response = await axios.post("/api/project", form.value);
        alert("Project created successfully!");

        fetchProject();
        closeModal();
    } catch (error) {
        console.error("Error creating Project:", error);
        alert("Failed to create Project.");
    }
}

const updateProject = async () => {
    try {
        const response = await axios.patch(`/api/project/${form.value.RowId}`, {
            name: form.value.name,
            is_location: form.value.is_location,
            is_project_location: form.value.is_project_location,
            status: form.value.status,
        });

        alert("Project updated successfully!");
        fetchProject();
        closeModal();
    } catch (error) {
        console.error("Error updating Project:", error);
        alert("Failed to update Project.");
    }
}

// const remove = async (id) => {
//     const confirmDelete = confirm("Are you sure you want to delete this asset type?");
//     if (confirmDelete) {
//         axios.delete(`/api/asset-type/${id}`)
//             .then((response) => {
//                 if (response.data.success) {
//                     alert("Asset type deleted successfully!");
//                     fetchAssetType();
//                 } else {
//                     alert("Failed to delete asset type.");
//                 }
//             })
//             .catch((error) => {
//                 console.error("Error deleting asset type:", error);
//                 alert("An error occurred while deleting the asset type.");
//             });
//     }
// }
</script>

<style scoped>
.asset-transaksi-page {
    padding: 20px;
}
</style>
