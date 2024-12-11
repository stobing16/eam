<template>
    <div class="asset-hirarki-page">
        <div class="mb-2">
            <h1 class="page-title mb-2">Asset Hirarki</h1>
        </div>
        <div class="d-flex justify-content-end mb-2">
            <button class="btn btn-primary" @click="openCreateModal">Add Main Group</button>
        </div>

        <!-- <div class="search-bar mb-2">
            <input type="text" class="form-control" v-model="search" @input="fetchEmployees"
                placeholder="Search employees..." />
        </div> -->

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Main Group Code</th>
                        <th>Main Group</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(item, index) in mainGroup" :key="item.RowId">
                        <tr>
                            <!-- <td>{{ index + 1 + (pagination.currentPage - 1) * pagination.rowsPerPage }}</td> -->
                            <td @click.prevent="toggleExpand(item.RowId)">
                                <i :class="{ 'bi bi-plus-square': !isExpanded(item.RowId), 'bi bi-dash-square': isExpanded(item.RowId) }"
                                    style="cursor: pointer;"></i>
                            </td>
                            <td>{{ item.MainGroupCode }}</td>
                            <td>{{ item.MainGroupName }}</td>
                            <td>{{ item.Status === 'A' ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" @click="openEditModal(item)">Edit</button>
                                <button class="btn btn-danger btn-sm" @click="remove(item.RowId)">Delete</button>
                            </td>

                        </tr>
                        <tr>
                            <td colspan="5" v-if="isExpanded(item.RowId)">
                                <AssetType :id="item.RowId" :code="item.MainGroupCode" />
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
        </div>
        <div class="pagination">
            <button class="btn btn-secondary" :disabled="pagination.currentPage === 1"
                @click="fetchMainGroup(pagination.currentPage - 1)">
                Previous
            </button>
            <span>Page {{ pagination.currentPage }} of {{ pagination.totalPages }}</span>
            <button class="btn btn-secondary" :disabled="pagination.currentPage === pagination.totalPages"
                @click="fetchMainGroup(pagination.currentPage + 1)">
                Next
            </button>
        </div>

        <div class="modal" tabindex="-1" :class="{ show: showModal }" style="display: block;" v-if="showModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Edit Main Group' : 'Add Main Group' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3" v-if="isEditing">
                            <label for="name">Main Group Name</label>
                            <input type="text" id="code" class="form-control" placeholder="Main Group Code"
                                v-model="form.code" disabled />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Main Group Name</label>
                            <input type="text" id="name" class="form-control" placeholder="Main Group Name"
                                v-model="form.name" />
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
                            @click="isEditing ? updateMainGroup() : createMainGroup()">
                            {{ isEditing ? 'Update' : 'Submit' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref } from 'vue';
import AssetType from '../../components/asset-hirarki/AssetType.vue';

const expandedRows = ref([]);
const mainGroup = ref([]);
const search = ref('');
const pagination = ref({
    currentPage: 1,
    rowsPerPage: 10,
    totalPages: 1,
})

const showModal = ref(false)
const isEditing = ref(false)

const form = ref({
    name: '',
    status: '',
})

const toggleExpand = (RowId) => {
    const index = expandedRows.value.indexOf(RowId);
    if (index === -1) {
        expandedRows.value.push(RowId); // Expand the row
    } else {
        expandedRows.value.splice(index, 1); // Collapse the row
    }
};

// Function to check if a row is expanded
const isExpanded = (RowId) => {
    return expandedRows.value.includes(RowId);
};

onMounted(() => {
    fetchMainGroup()
})

const fetchMainGroup = async (page = 1) => {
    try {
        page = Number(page);
        const response = await axios.get("/api/main-group", {
            params: {
                per_page: pagination.value.rowsPerPage,
                current_page: page,
                search: search.value,
            },
        });

        const data = response.data;
        mainGroup.value = data.data;

        pagination.value.currentPage = Number(data.currentPage);
        pagination.value.rowsPerPage = data.rowsPerPage;
        pagination.value.totalPages = data.totalPages;

    } catch (error) {
        console.error("Error fetching employees:", error);
    }
}

const openCreateModal = () => {
    showModal.value = true
    isEditing.value = false
}

const openEditModal = (item) => {
    form.value.RowId = item.RowId
    form.value.code = item.MainGroupCode
    form.value.name = item.MainGroupName
    form.value.status = item.Status

    showModal.value = true;
    isEditing.value = true;
}

const closeModal = () => {
    showModal.value = false
    isEditing.value = false
}

const createMainGroup = async () => {
    try {
        const response = await axios.post("/api/main-group", form.value);
        alert("Main Group created successfully!");

        fetchMainGroup(pagination.value.currentPage);
        closeModal();
    } catch (error) {
        console.error("Error creating main group:", error);
        alert("Failed to create main group.");
    }
}

const updateMainGroup = async () => {
    try {
        const response = await axios.patch(`/api/main-group/${form.value.RowId}`, {
            name: form.value.name,
            code: form.value.code,
            status: form.value.status,
        });

        alert("Main Group updated successfully!");
        fetchMainGroup(pagination.value.currentPage);
        closeModal();
    } catch (error) {
        console.error("Error updating main group:", error);
        alert("Failed to update main group.");
    }
}

const remove = async (id) => {
    const confirmDelete = confirm("Are you sure you want to delete this main group?");
    if (confirmDelete) {
        axios.delete(`/api/main-group/${id}`)
            .then((response) => {
                if (response.data.success) {
                    alert("Main Group deleted successfully!");
                    fetchMainGroup(pagination.value.currentPage);
                } else {
                    alert("Failed to delete main group.");
                }
            })
            .catch((error) => {
                console.error("Error deleting main group:", error);
                alert("An error occurred while deleting the main group.");
            });
    }
}


</script>
<style scoped>
i:hover {
    -webkit-text-stroke: 1px;
    cursor: pointer;
}

.asset-hirarki-page {
    padding: 20px;
}
</style>
