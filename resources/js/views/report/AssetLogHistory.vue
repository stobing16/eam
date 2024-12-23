<template>
    <div class="asset-transaksi-page">
        <h1 class="page-title">Asset Log History</h1>
        <div class="search-bar">
            <input type="text" class="form-control" v-model="asset_code" @input="fetchAssetLogHistory"
                placeholder="Search Asset Code..." />
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Check Out Code</th>
                        <th>Asset Code</th>
                        <th>Check Out Date</th>
                        <th>Project Location</th>
                        <th>Check Out Notes</th>
                        <th>Check Out By</th>
                        <th>Check Out Delivery By</th>
                        <th>Check Out To</th>
                        <th>Expected Check In</th>
                        <th>Check In Date</th>
                        <th>Check In Notes</th>
                        <th>Check In Delivery By</th>
                        <th>Check In Received By</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(data, index) in assets" :key="data.CheckOutCode">
                        <td>{{ index + 1 }}</td>
                        <td>{{ data.CheckOutCode }}</td>
                        <td>{{ data.AssetCode }}</td>
                        <td>{{ data.CheckOutDate }}</td>
                        <td>{{ data.ProjectLocationName }}</td>
                        <td>{{ data.CheckOutNotes }}</td>
                        <td>{{ data.CheckOutBy }}</td>
                        <td>{{ data.CheckOutDeliveryBy }}</td>
                        <td>{{ data.CheckOutTo }}</td>
                        <td>{{ data.ExpectedCheckIn }}</td>
                        <td>{{ data.CheckInDate }}</td>
                        <td>{{ data.CheckInNotes }}</td>
                        <td>{{ data.CheckInDeliveryBy }}</td>
                        <td>{{ data.CheckInReceivedBy }}</td>
                        <td>{{ data.Status }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";

const assets = ref([])
const asset_code = ref("")

onMounted(() => {
    fetchAssetLogHistory()
})

const fetchAssetLogHistory = async () => {
    try {
        const response = await axios.get("/api/report/asset-log-history", {
            params: {
                asset_code: asset_code.value,
            },
        });
        console.log("response", response)

        const data = response;
        assets.value = data.data;
        // console.log(assets.value)
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
