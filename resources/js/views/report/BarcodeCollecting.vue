<template>
    <div class="asset-transaksi-page">
        <h1 class="page-title">Barcode Collection</h1>
        <div class="search-bar">
            <input type="text" class="form-control" v-model="asset_code" @input="fetchBarcodeCollecting"
                placeholder="Search Barcode Collecting..." />
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Request Date</th>
                        <th>Asset Code</th>
                        <th>Asset Name</th>
                        <th>Brand Name</th>
                        <th>Model Name</th>
                        <th>Project</th>
                        <th>Location</th>
                        <th>Sub Location</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Request By</th>
                        <th>Check Out To</th>
                        <th>Acknowledge By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(data, index) in assets" :key="data.TrxId">
                        <td>{{ data.TrxId }}</td>
                        <td>{{ data.CreatedDate }}</td>
                        <td>{{ data.AssetCode }}</td>
                        <td>{{ data.AssetName }}</td>
                        <td>{{ data.BrandName }}</td>
                        <td>{{ data.ModelName }}</td>
                        <td>{{ data.ProjectName }}</td>
                        <td>{{ data.LocationName }}</td>
                        <td>{{ data.Status }}</td>
                        <td>{{ data.SubLocationName }}</td>
                        <td>{{ data.Notes }}</td>
                        <td>{{ data.RequestBy }}</td>
                        <td>{{ data.CheckOut }}</td>
                        <td>{{ data.AcknowledgeBy }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <button class="btn btn-secondary" :disabled="pagination.currentPage === 1"
                @click="fetchBarcodeCollecting(pagination.currentPage - 1)">
                Previous
            </button>
            <span>Page {{ pagination.currentPage }} of {{ pagination.totalPages }}</span>
            <button class="btn btn-secondary" :disabled="pagination.currentPage === pagination.totalPages"
                @click="fetchBarcodeCollecting(pagination.currentPage + 1)">
                Next
            </button>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";

const assets = ref([])
const asset_code = ref("")
const pagination = ref({
    currentPage: 1,
    rowsPerPage: 10,
    totalPages: 1,
})

onMounted(() => {
    fetchBarcodeCollecting()
})

const fetchBarcodeCollecting = async (page = 1) => {
    try {
        page = Number(page);

        const response = await axios.get("/api/report/barcode-collecting", {
            params: {
                per_page: pagination.value.rowsPerPage,
                current_page: page,
                asset_code: asset_code.value,
            },
        });

        const data = response.data;
        assets.value = data.data;
        // console.log(assets.value)

        pagination.value.currentPage = Number(data.currentPage);
        pagination.value.rowsPerPage = data.rowsPerPage;
        pagination.value.totalPages = data.totalPages;
    } catch (error) {
        console.error("Error fetching Assets:", error);
    }
}
</script>

<style scoped>
.asset-transaksi-page {
    padding: 20px;
}
</style>
