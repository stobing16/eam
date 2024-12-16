<template>
    <div class="company-page">
        <h1 class="page-title">Company List</h1>
        <!-- Button to trigger the modal to create a new employee -->
        <div class="d-flex mb-3 justify-content-end gap-2">
            <button class="btn btn-primary" @click="openCreateModal">Add Company</button>
            <!-- <button class="btn btn-primary" @click="openExcelCreateModal">Export Excel</button> -->
        </div>
        <div class="search-bar">
            <input type="text" class="form-control" v-model="search" @input="fetchCompany"
                placeholder="Search companies..." />
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Company ID</th>
                        <th>Company Name</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(company, index) in companies" :key="company.RowId">
                        <td>{{ index + 1 + ((pagination.currentPage - 1) * pagination.rowsPerPage) }}</td>
                        <td>{{ company.CompanyId }}</td>
                        <td>{{ company.CompanyName }}</td>
                        <td>{{ company.Status === 'A' ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" @click="openEditModal(company)">Edit</button>
                            <!-- <button class="btn btn-danger btn-sm" @click="deleteEmployee(company.RowId)">Delete</button> -->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <button class="btn btn-secondary" :disabled="pagination.currentPage === 1"
                @click="fetchCompany(pagination.currentPage - 1)">
                Previous
            </button>
            <span>Page {{ pagination.currentPage }} of {{ pagination.totalPages }}</span>
            <button class="btn btn-secondary" :disabled="pagination.currentPage === pagination.totalPages"
                @click="fetchCompany(pagination.currentPage + 1)">
                Next
            </button>
        </div>


        <div class="modal" tabindex="-1" :class="{ show: showModal }" style="display: block;" v-if="showModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Edit Company' : 'Create Company' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group" v-if="isEditing">
                            <label for="name">Company Code</label>
                            <input type="text" id="name" class="form-control" v-model="form.code" disabled />
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" class="form-control" v-model="form.name" />
                        </div>
                        <div class="form-group" v-if="isEditing">
                            <label for="status">Status</label>
                            <select id="status" class="form-control" v-model="form.status">
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-primary"
                            @click="isEditing ? updateCompany() : createCompany()">
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
import * as XLSX from 'xlsx';

const companies = ref([])
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
    fetchCompany()
})

const fetchCompany = async (page = 1) => {
    try {
        page = Number(page);

        const response = await axios.get("/api/company", {
            params: {
                per_page: pagination.value.rowsPerPage,
                current_page: page,
                search: search.value,
            },
        });

        const data = response.data;
        companies.value = data.data;
        pagination.value.currentPage = Number(data.currentPage);
        pagination.value.rowsPerPage = data.rowsPerPage;
        pagination.value.totalPages = data.totalPages;
    } catch (error) {
        console.error("Error fetching companies:", error);
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
    form.value.code = item.CompanyId
    form.value.name = item.CompanyName
    form.value.status = item.Status

    showModal.value = true;
    isEditing.value = true;
}

const createCompany = async () => {
    try {
        const response = await axios.post("/api/company", form.value);
        alert("Company created successfully!");

        fetchCompany();
        closeModal();
    } catch (error) {
        console.error("Error creating company:", error);
        alert("Failed to create company.");
    }
}

const updateCompany = async () => {
    try {
        const response = await axios.patch(`/api/company/${form.value.RowId}`, {
            name: form.value.name,
            status: form.value.status,
        });

        alert("Company updated successfully!");
        fetchCompany();
        closeModal();
    } catch (error) {
        console.error("Error updating Company:", error);
        alert("Failed to update Company.");
    }
}

const remove = async (id) => {
    const confirmDelete = confirm("Are you sure you want to delete this asset type?");
    if (confirmDelete) {
        axios.delete(`/api/asset-type/${id}`)
            .then((response) => {
                if (response.data.success) {
                    alert("Asset type deleted successfully!");
                    fetchAssetType();
                } else {
                    alert("Failed to delete asset type.");
                }
            })
            .catch((error) => {
                console.error("Error deleting asset type:", error);
                alert("An error occurred while deleting the asset type.");
            });
    }
}
</script>

<style scoped>
.company-page {
    padding: 20px;
}
</style>
