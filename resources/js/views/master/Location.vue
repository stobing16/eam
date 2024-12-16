<template>
    <div class="location-page">
        <h1 class="page-title">Location List</h1>
        <!-- Button to trigger the modal to create a new employee -->
        <div class="d-flex mb-3 justify-content-end gap-2">
            <button class="btn btn-primary" @click="openCreateModal">Add Location</button>
            <!-- <button class="btn btn-primary" @click="openExcelCreateModal">Export Excel</button> -->
        </div>
        <div class="search-bar">
            <input type="text" class="form-control" v-model="search" @input="fetchLocation"
                placeholder="Search location..." />
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Location Code</th>
                        <th>Location Name</th>
                        <th>Is Location</th>
                        <th>Is Project Location</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(location, index) in locations" :key="location.RowId">
                        <td>{{ index + 1 + ((pagination.currentPage - 1) * pagination.rowsPerPage) }}</td>
                        <td>{{ location.LocationCode }}</td>
                        <td>{{ location.LocationName }}</td>
                        <td>{{ location.IsLocation == 1 ? 'Ya' : 'Tidak' }}</td>
                        <td>{{ location.IsProjectLocation == 1 ? 'Ya' : 'Tidak' }}</td>
                        <td>{{ location.Status === 'A' ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" @click="openEditModal(location)">Edit</button>
                            <!-- <button class="btn btn-danger btn-sm" @click="deleteEmployee(location.RowId)">Delete</button> -->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <button class="btn btn-secondary" :disabled="pagination.currentPage === 1"
                @click="fetchLocation(pagination.currentPage - 1)">
                Previous
            </button>
            <span>Page {{ pagination.currentPage }} of {{ pagination.totalPages }}</span>
            <button class="btn btn-secondary" :disabled="pagination.currentPage === pagination.totalPages"
                @click="fetchLocation(pagination.currentPage + 1)">
                Next
            </button>
        </div>


        <div class="modal" tabindex="-1" :class="{ show: showModal }" style="display: block;" v-if="showModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Edit Location' : 'Add Location' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3" v-if="isEditing">
                            <label for="name">Location Code</label>
                            <input type="text" class="form-control" v-model="form.code" disabled />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Location Name</label>
                            <input type="text" class="form-control" v-model="form.name" />
                        </div>
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="form.is_location"
                                    id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Is Location
                                </label>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="form.is_project_location"
                                    id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Is Project Location
                                </label>
                            </div>
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
                            @click="isEditing ? updateLocation() : createLocation()">
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

const locations = ref([])
const search = ref("")
const pagination = ref({
    currentPage: 1,
    rowsPerPage: 10,
    totalPages: 1,
})

const form = ref({
    name: '',
    is_location: false,
    is_project_location: false
})

const showModal = ref(false)
const isEditing = ref(false)

onMounted(() => {
    fetchLocation()
})

const fetchLocation = async (page = 1) => {
    try {
        page = Number(page);

        const response = await axios.get("/api/location", {
            params: {
                per_page: pagination.value.rowsPerPage,
                current_page: page,
                search: search.value,
            },
        });

        const data = response.data;
        locations.value = data.data;
        pagination.value.currentPage = Number(data.currentPage);
        pagination.value.rowsPerPage = data.rowsPerPage;
        pagination.value.totalPages = data.totalPages;
    } catch (error) {
        console.error("Error fetching locations:", error);
    }
}

const openCreateModal = () => {
    showModal.value = true
    isEditing.value = false
}

const closeModal = () => {
    showModal.value = false
    isEditing.value = false
}

const openEditModal = (item) => {
    console.log(item)
    form.value.RowId = item.RowId
    form.value.code = item.LocationCode
    form.value.name = item.LocationName
    form.value.is_location = item.IsLocation == 1 ? true : false
    form.value.is_project_location = item.IsProjectLocation == 1 ? true : false
    form.value.status = item.Status

    showModal.value = true;
    isEditing.value = true;
}

const createLocation = async () => {
    try {
        const response = await axios.post("/api/location", form.value);
        alert("Location created successfully!");

        fetchLocation();
        closeModal();
    } catch (error) {
        console.error("Error creating Location:", error);
        alert("Failed to create Location.");
    }
}

const updateLocation = async () => {
    try {
        const response = await axios.patch(`/api/location/${form.value.RowId}`, {
            name: form.value.name,
            is_location: form.value.is_location,
            is_project_location: form.value.is_project_location,
            status: form.value.status,
        });

        alert("Location updated successfully!");
        fetchLocation();
        closeModal();
    } catch (error) {
        console.error("Error updating Location:", error);
        alert("Failed to update Location.");
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
.location-page {
    padding: 20px;
}
</style>
