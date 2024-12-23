<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
const { id } = defineProps({
    id: String
})

const router = useRouter();

const form = ref({
    model: '',
    checkout_code: '',
    asset_code: '',
    asset_name: '',
    checkout_date: '',
    expected_checkin: '',
    checkout_notes: '',
    checkout_project_location: '',
    checkout_to: '',
    checkin_date: '',
    condition: '',
    received_by: '',
    delivered_by: '',
    checkin_location: '',
    notes: '',
    status: '',
});

const location = ref([])
const employee = ref([])
const condition = ref([])

onMounted(() => {
    fetchAssetCheckin()
    fetchList()
})
const fetchAssetCheckin = async () => {
    try {
        const response = await axios.get(`/api/transaksi/assets/${id}/check-in`);

        const data = response.data;
        console.log(data)
        form.value.model = data.model
        form.value.asset_code = data.assetCode
        form.value.asset_name = data.assetName
        form.value.checkout_code = data.checkout_code
        form.value.checkout_date = data.checkout_date
        form.value.expected_checkin = data.expected_checkin_date
        form.value.checkout_notes = data.checkout_notes
        form.value.checkout_project_location = data.checkout_project_location
        form.value.checkout_to = data.checkout_to
    } catch (error) {
        console.error("Error fetching Asset Checkin Data:", error);
    }
}
const fetchList = async () => {
    try {
        const response = await axios.get("/api/transaksi/assets/list");

        const data = response.data.data;

        location.value = data.location
        employee.value = data.employee
        condition.value = data.condition
    } catch (error) {
        console.error("Error fetching Supported Data:", error);
    }
}

const submitCheckin = async () => {
    try {
        const response = await axios.post("/api/transaksi/assets/checkin", form.value);
        alert("Asset Checkin inserted successfully!");

        router.back()
    } catch (error) {
        console.error("Error creating Asset:", error);
        alert("Failed to insert Checkin Asset.");
    }
}

</script>
<template>
    <div class="asset-checkout-transaksi-page">
        <h1 class="page-title">Asset Checkin</h1>
        <form class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="code">Model</label>
                        <input type="text" class="form-control" v-model="form.model" disabled />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="code">Checkout Code</label>
                        <input type="text" class="form-control" v-model="form.checkout_code" disabled />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="code">Asset Code</label>
                        <input type="text" class="form-control" v-model="form.asset_code" disabled />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Asset Name</label>
                        <input type="text" class="form-control" v-model="form.asset_name" disabled />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Checkout Date</label>
                        <input type="text" class="form-control" v-model="form.checkout_date" disabled />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Expected CheckIn Date</label>
                        <input type="text" class="form-control" v-model="form.expected_checkin" disabled />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Checkout Notes</label>
                        <input type="text" class="form-control" v-model="form.checkout_notes" disabled />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Checkout Project Location</label>
                        <input type="text" class="form-control" v-model="form.checkout_project_location" disabled />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Checkout To</label>
                        <input type="text" class="form-control" v-model="form.checkout_to" disabled />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Checkin Date</label>
                        <input type="date" class="form-control" v-model="form.checkin_date" />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="condition">Condition</label>
                        <select id="condition" class="form-control" v-model="form.condition">
                            <option value="">Select Condition</option>
                            <option v-for="item in condition" :value="item.ChildId">
                                {{ item.PlDescription }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="received_by">Received By</label>
                        <select id="received_by" class="form-control" v-model="form.received_by">
                            <option value="">Select User</option>
                            <option v-for="item in employee" :value="item.NIK">
                                {{ item.NIK + ' - ' + item.Nama }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="delivered_by">Delivered By</label>
                        <select id="delivered_by" class="form-control" v-model="form.delivered_by">
                            <option value="">Select User</option>
                            <option v-for="item in employee" :value="item.NIK">
                                {{ item.NIK + ' - ' + item.Nama }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="checkin_location">Checkin Location</label>
                        <select id="checkin_location" class="form-control" v-model="form.checkin_location">
                            <option value="">Select Location</option>
                            <option v-for="item in location" :value="item.LocationCode">
                                {{ item.LocationName }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Notes</label>
                        <input type="text" placeholder="Notes" class="form-control" v-model="form.notes" />
                    </div>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-secondary" @click="router.back()">Cancel</button>
            <button type="button" class="btn btn-primary" @click="submitCheckin()">Submit</button>
        </div>
    </div>

</template>
<style scoped>
.asset-checkout-transaksi-page {
    padding: 20px;
}
</style>
