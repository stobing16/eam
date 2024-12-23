<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
const { id } = defineProps({
    id: String
})
const router = useRouter();

const form = ref({
    asset_code: '',
    asset_name: '',
    model: '',
    purchase_date: '',
    checkout_to: '',
    sub_location: '',
    project_location_code: '',
    project_code: '',
    checkout_date: '',
    expected_checkin: '',
    notes: '',
    checkout_by: '',
    delivered_by: '',
    acknowledge_by: '',
    status: '',
});

const location = ref([])
const subLocation = ref([])
const project = ref([])
const employee = ref([])

onMounted(() => {
    fetchAssetCheckout()
    fetchList()
})
const fetchAssetCheckout = async () => {
    try {
        const response = await axios.get(`/api/transaksi/assets/${id}/check-out`);

        const data = response.data;
        form.value.asset_code = data.assetCode
        form.value.asset_name = data.assetName
        form.value.model = data.model
        form.value.status = data.status
        form.value.purchase_date = data.purchaseDate
        form.value.last_checkin = data.lastCheckIn
    } catch (error) {
        console.error("Error fetching Asset Checkout  Data:", error);
    }
}
const fetchList = async () => {
    try {
        const response = await axios.get("/api/transaksi/assets/list");

        const data = response.data.data;

        location.value = data.location
        subLocation.value = data.subLocation
        employee.value = data.employee
        project.value = data.project
    } catch (error) {
        console.error("Error fetching Supported Data:", error);
    }
}

const submitCheckout = async () => {
    try {
        const response = await axios.post("/api/transaksi/assets/checkout", form.value);
        alert("Asset Checkout data inserted successfully!");

        router.back()
    } catch (error) {
        console.error("Error creating Asset:", error);
        alert("Failed to insert checkout Asset.");
    }
}

</script>
<template>
    <div class="asset-checkout-transaksi-page">
        <h1 class="page-title">Asset Checkout</h1>
        <form class="mb-3">
            <div class="row">
                <!-- Project Code (Only shown when editing) -->
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="code">Asset Code</label>
                        <input type="text" class="form-control" v-model="form.asset_code" disabled />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="code">Asset Name</label>
                        <input type="text" class="form-control" v-model="form.asset_name" disabled />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="code">Model</label>
                        <input type="text" class="form-control" v-model="form.model" disabled />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Purchase Date</label>
                        <input type="text" class="form-control" v-model="form.purchase_date" disabled />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Last CheckIn</label>
                        <input type="text" class="form-control" v-model="form.last_checkin" disabled />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="project_location_code">Checkout Location</label>
                        <select id="project_location_code" class="form-control" v-model="form.project_location_code">
                            <option value="">Select Location</option>
                            <option v-for="item in location" :value="item.LocationCode">
                                {{ item.LocationName }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="sub_location">Checkout Sub Location</label>
                        <select id="sub_location" class="form-control" v-model="form.sub_location">
                            <option value="">Select Sub Location</option>
                            <option v-for="item in subLocation" :value="item.SubLocationCode">
                                {{ item.SubLocationName }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Checkout Date</label>
                        <input type="date" class="form-control" v-model="form.checkout_date" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Expected CheckIn Date</label>
                        <input type="date" class="form-control" v-model="form.expected_checkin" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="checkout_project_to">Checkout Project To</label>
                        <select id="checkout_project_to" class="form-control" v-model="form.project_code">
                            <option value="">Select Project</option>
                            <option v-for="item in project" :value="item.ProjectCode">
                                {{ item.ProjectName }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="checkout_to">Checkout To</label>
                        <select id="checkout_to" class="form-control" v-model="form.checkout_to">
                            <option value="">Select User</option>
                            <option v-for="item in employee" :value="item.NIK">
                                {{ item.NIK + ' - ' + item.Nama }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="checkout_by">Checkout By</label>
                        <select id="checkout_by" class="form-control" v-model="form.checkout_by">
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
                        <label for="acknowledged_by">Acknowledged By</label>
                        <select id="acknowledged_by" class="form-control" v-model="form.acknowledge_by">
                            <option value="">Select User</option>
                            <option v-for="item in employee" :value="item.NIK">
                                {{ item.NIK + ' - ' + item.Nama }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Notes</label>
                        <input type="text" class="form-control" v-model="form.notes" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="code">Status</label>
                        <input type="text" class="form-control" v-model="form.status" disabled />
                    </div>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-secondary" @click="router.back()">Cancel</button>
            <button type="button" class="btn btn-primary" @click="submitCheckout()">Submit</button>
        </div>
    </div>

</template>
<style scoped>
.asset-checkout-transaksi-page {
    padding: 20px;
}
</style>
