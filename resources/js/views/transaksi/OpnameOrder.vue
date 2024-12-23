<script setup>
import { nextTick, onMounted, ref } from 'vue';

const opname_order_lists = ref([])
const locations = ref([])

const search = ref("")
const pagination = ref({
    currentPage: 1,
    rowsPerPage: 10,
    totalPages: 1,
})

const showModal = ref(false)
const isEditing = ref(false)

const fetchOpnameOrder = async (page = 1) => {
    try {
        page = Number(page);

        const response = await axios.get("/api/transaksi/opname", {
            params: {
                per_page: pagination.value.rowsPerPage,
                current_page: page,
                search: search.value,
            },
        });

        const data = response.data;
        opname_order_lists.value = data.data;
        pagination.value.currentPage = Number(data.currentPage);
        pagination.value.rowsPerPage = data.rowsPerPage;
        pagination.value.totalPages = data.totalPages;
    } catch (error) {
        console.error("Error fetching Assets:", error);
    }
}

const fetchLocation = async () => {
    try {
        const response = await axios.get("/api/transaksi/opname/loc");

        const data = response.data;
        locations.value = data
    } catch (error) {
        console.error("Error fetching Loc Assets:", error);
    }
}

const form = ref({
    location: '',
    date: '',
    category: '',
});

const openCreateModal = () => {
    showModal.value = true
    isEditing.value = false
}

const closeModal = () => {
    form.value = {
        location: '',
        date: '',
        category: '',
    }
    nextTick(() => {
        showModal.value = false
        isEditing.value = false
    });
}

const openEditModal = (item) => {
    form.value.RowId = item.RowId;
    form.value.id = item.OpnameOrderId;
    form.value.location = item.LocationCode;
    form.value.date = item.OpnameOrderDate;
    form.value.category = item.OpnameOrderType;
    form.value.statusResult = item.StatusResult;
    form.value.status = item.Status;

    nextTick(() => {
        showModal.value = true;
        isEditing.value = true;
    });
}

const createOpname = async () => {
    try {
        await axios.post("/api/transaksi/opname", form.value);
        alert("Opname Order created successfully!");

        fetchOpnameOrder();
        closeModal();
    } catch (error) {
        console.error("Error creating Opname Order:", error);
        alert("Failed to create Opname Order.");
    }
};

const updateOpname = async () => {
    try {
        console.log(form.value)
        const response = await axios.patch(`/api/transaksi/opname/${form.value.RowId}`, form.value);

        alert("Opname Order updated successfully!");
        fetchOpnameOrder();
        closeModal();
    } catch (error) {
        console.error("Error updating Opname Order:", error);
        alert("Failed to update Opname Order.");
    }
}


onMounted(() => {
    fetchOpnameOrder()
    fetchLocation()
})

</script>
<template>
    <div class="asset-transaksi-page">
        <h1 class="page-title">Opname Order</h1>

        <!-- Button to trigger the modal to create a new employee -->
        <div class="d-flex mb-3 justify-content-end gap-2">
            <button class="btn btn-primary" @click="openCreateModal">Add New Opname</button>
        </div>
        <div class="search-bar">
            <input type="text" class="form-control" v-model="search" @input="fetchOpnameOrder"
                placeholder="Search..." />
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in opname_order_lists" :key="item.RowId">
                        <td>{{ index + 1 + ((pagination.currentPage - 1) * pagination.rowsPerPage) }}</td>
                        <td>{{ item.OpnameOrderDate }}</td>
                        <td>{{ item.location.LocationName }}</td>
                        <td>{{ item.OpnameOrderType == 1 ? 'Rent' : 'Asset' }}</td>
                        <td>
                            {{ item.StatusResult }}
                        </td>
                        <td>
                            <div class="d-flex flex-wrap">
                                <template v-if="item.StatusResult == 'Open'">
                                    <button title="Download Opname File"
                                        class="btn btn-primary d-flex align-items-center">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button class="btn btn-success btn-sm" @click="openEditModal(item)">
                                        Edit
                                    </button>
                                </template>
                                <router-link :to="{ name: 'opname-order-details', params: { id: item.RowId } }">
                                    <button class="btn btn-warning btn-sm">
                                        Detail
                                    </button>
                                </router-link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <button class="btn btn-secondary" :disabled="pagination.currentPage === 1"
                @click="fetchOpnameOrder(pagination.currentPage - 1)">
                Previous
            </button>
            <span>Page {{ pagination.currentPage }} of {{ pagination.totalPages }}</span>
            <button class="btn btn-secondary" :disabled="pagination.currentPage === pagination.totalPages"
                @click="fetchOpnameOrder(pagination.currentPage + 1)">
                Next
            </button>
        </div>

        <!-- Modal Create / Update / Show -->
        <div class="modal" tabindex="-1" :class="{ show: showModal }" style="display: block;" v-if="showModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Edit Opname Order' : 'Add New Opname Order' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group mb-3" v-if="isEditing">
                                <label for="code">Opname Order Id</label>
                                <input type="text" class="form-control" v-model="form.id" disabled />
                            </div>
                            <div class="form-group mb-3">
                                <label for="location">Location</label>
                                <select id="location" class="form-control" v-model="form.location">
                                    <option value="">Select Location</option>
                                    <option v-for="item in locations" :value="item.LocationCode">
                                        {{ item.LocationName }}
                                    </option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="date">Date</label>
                                <input id="date" :type="isEditing ? 'text' : 'date'" class="form-control"
                                    :disabled="isEditing" v-model="form.date" />
                            </div>

                            <div class="form-group mb-3">
                                <label for="category">Category</label>
                                <select id="category" class="form-control" v-model="form.category">
                                    <option value="">Select Category</option>
                                    <option value="2">Asset</option>
                                </select>
                            </div>

                            <div class="form-group mb-3" v-if="isEditing">
                                <label for="code">Opname Order Status</label>
                                <input type="text" class="form-control" v-model="form.statusResult" disabled />
                            </div>

                            <!-- Status (Only shown when editing) -->
                            <div class="form-group mb-3" v-if="isEditing">
                                <label for="status">Status</label>
                                <select id="status" class="form-control" v-model="form.status">
                                    <option value="A">Active</option>
                                    <option value="I">Inactive</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-primary"
                            @click="isEditing ? updateOpname() : createOpname()">
                            {{ isEditing ? 'Update' : 'Submit' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
