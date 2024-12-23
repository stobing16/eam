<script setup>
import { onMounted, ref } from 'vue';
const { id } = defineProps({
    id: String
})

onMounted(() => {
    fetchOpname()
})

const opname = ref({})
const detail_opname = ref([])
const locations = ref([])

const search = ref("")
const pagination = ref({
    currentPage: 1,
    rowsPerPage: 10,
    totalPages: 1,
})

const fetchOpname = async (page = 1) => {
    try {
        page = Number(page);

        const response = await axios.get(`/api/transaksi/opname/${id}`, {
            params: {
                per_page: pagination.value.rowsPerPage,
                current_page: page,
                search: search.value,
            },
        });

        opname.value = response.data.opname;
        detail_opname.value.data = response.data.details.data;
        pagination.value.currentPage = Number(response.data.details.currentPage);
        pagination.value.rowsPerPage = response.data.details.rowsPerPage;
        pagination.value.totalPages = response.data.details.totalPages;

    } catch (error) {
        console.error("Error fetching Loc Assets:", error);
    }
}
</script>
<template>
    <div class="asset-transaksi-page">
        <h1 class="page-title mb-3">Opname Order List</h1>

        <div class="search-bar">
            <label for="search" class="pl-3">Opname Order Id</label>
            <input type="text" class="form-control" :value="opname?.OpnameOrderId" disabled />
        </div>

        <div class="search-bar">
            <label for="search" class="pl-3">Status</label>
            <input type="text" class="form-control" :value="opname?.StatusResult" disabled />
        </div>

        <div class="search-bar">
            <label for="search" class="pl-3">Location</label>
            <input type="text" class="form-control" :value="opname?.location?.LocationName" disabled />
        </div>

        <!-- <div class="d-flex mb-3 justify-content-end gap-2">
            <button class="btn btn-primary" @click="openCreateModal">Add New Opname</button>
        </div> -->
        <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6 ">
                <div class="search-bar">
                    <label for="search" class="pl-3">Search</label>
                    <input type="text" class="form-control" v-model="search" @input="fetchOpname"
                        placeholder="Search..." />
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Info</th>
                        <th>Condition</th>
                        <th>Model</th>
                        <th>Created By</th>
                        <th>Created Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in detail_opname.data" :key="item.RowId">
                        <td>{{ item.RowId }}</td>
                        <td>{{ item.Info }}</td>
                        <td>{{ item.Condition }}</td>
                        <td>{{ item.ModelName }}</td>
                        <td>{{ item.CreatedBy }}</td>
                        <td>{{ item.CreatedDate }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <button class="btn btn-secondary" :disabled="pagination.currentPage === 1"
                @click="fetchOpname(pagination.currentPage - 1)">
                Previous
            </button>
            <span>Page {{ pagination.currentPage }} of {{ pagination.totalPages }}</span>
            <button class="btn btn-secondary" :disabled="pagination.currentPage === pagination.totalPages"
                @click="fetchOpname(pagination.currentPage + 1)">
                Next
            </button>
        </div>
    </div>
</template>
<style scoped></style>
