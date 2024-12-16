<template>
    <div class="supplier-page">
        <h1 class="page-title">Supplier List</h1>
        <!-- Button to trigger the modal to create a new employee -->
        <div class="d-flex mb-3 justify-content-end gap-2">
            <button class="btn btn-primary" @click="openCreateModal">Add Supplier</button>
            <!-- <button class="btn btn-primary" @click="openExcelCreateModal">Export Excel</button> -->
        </div>
        <div class="search-bar">
            <input type="text" class="form-control" v-model="search" @input="fetchSupplier"
                placeholder="Search supplier..." />
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Supplier Code</th>
                        <th>Supplier Name</th>
                        <th>Phone</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(supplier, index) in suppliers" :key="supplier.RowId">
                        <td>{{ index + 1 + ((pagination.currentPage - 1) * pagination.rowsPerPage) }}</td>
                        <td>{{ supplier.SupplierCode }}</td>
                        <td>{{ supplier.SupplierName }}</td>
                        <td>{{ supplier.Phone }}</td>
                        <td>{{ supplier.Mobile }}</td>
                        <td>{{ supplier.Status === 'A' ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" @click="openEditModal(supplier)">Edit</button>
                            <!-- <button class="btn btn-danger btn-sm" @click="deleteEmployee(Supplier.RowId)">Delete</button> -->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <button class="btn btn-secondary" :disabled="pagination.currentPage === 1"
                @click="fetchSupplier(pagination.currentPage - 1)">
                Previous
            </button>
            <span>Page {{ pagination.currentPage }} of {{ pagination.totalPages }}</span>
            <button class="btn btn-secondary" :disabled="pagination.currentPage === pagination.totalPages"
                @click="fetchSupplier(pagination.currentPage + 1)">
                Next
            </button>
        </div>


        <div class="modal" tabindex="-1" :class="{ show: showModal }" style="display: block;" v-if="showModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Edit Supplier' : 'Create Supplier' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3" v-if="isEditing">
                            <label for="name">Supplier Code</label>
                            <input type="text" class="form-control" v-model="form.code" disabled />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Supplier Name</label>
                            <input type="text" class="form-control" v-model="form.name" />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Phone</label>
                            <input type="text" class="form-control" v-model="form.phone" />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Mobile</label>
                            <input type="text" class="form-control" v-model="form.mobile" />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Address</label>
                            <input type="text" class="form-control" v-model="form.address" />
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
                            @click="isEditing ? updateSupplier() : createSupplier()">
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

const suppliers = ref([])
const search = ref("")
const pagination = ref({
    currentPage: 1,
    rowsPerPage: 10,
    totalPages: 1,
})

const form = ref({
    name: '',
    phone: '',
    mobile: '',
    address: '',
})

const showModal = ref(false)
const isEditing = ref(false)

onMounted(() => {
    fetchSupplier()
})

const fetchSupplier = async (page = 1) => {
    try {
        page = Number(page);

        const response = await axios.get("/api/supplier", {
            params: {
                per_page: pagination.value.rowsPerPage,
                current_page: page,
                search: search.value,
            },
        });

        const data = response.data;
        suppliers.value = data.data;
        pagination.value.currentPage = Number(data.currentPage);
        pagination.value.rowsPerPage = data.rowsPerPage;
        pagination.value.totalPages = data.totalPages;
    } catch (error) {
        console.error("Error fetching suppliers:", error);
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
    form.value.code = item.SupplierCode
    form.value.name = item.SupplierName
    form.value.mobile = item.Mobile
    form.value.phone = item.Phone
    form.value.address = item.Address
    form.value.status = item.Status

    showModal.value = true;
    isEditing.value = true;
}

const createSupplier = async () => {
    try {
        const response = await axios.post("/api/supplier", form.value);
        alert("Supplier created successfully!");

        fetchSupplier();
        closeModal();
    } catch (error) {
        console.error("Error creating Supplier:", error);
        alert("Failed to create Supplier.");
    }
}

const updateSupplier = async () => {
    try {
        const response = await axios.patch(`/api/supplier/${form.value.RowId}`, {
            name: form.value.name,
            phone: form.value.phone,
            mobile: form.value.mobile,
            address: form.value.address,
            status: form.value.status,
        });

        alert("Supplier updated successfully!");
        fetchSupplier();
        closeModal();
    } catch (error) {
        console.error("Error updating Supplier:", error);
        alert("Failed to update Supplier.");
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
.supplier-page {
    padding: 20px;
}
</style>
