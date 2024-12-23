<template>
    <div class="asset-transaksi-page">
        <h1 class="page-title">Asset</h1>
        <!-- Button to trigger the modal to create a new employee -->
        <div class="d-flex mb-3 justify-content-end gap-2">
            <button class="btn btn-primary" @click="openCreateModal">Add New Asset</button>
            <!-- <button class="btn btn-primary" @click="openExcelCreateModal">Export Excel</button> -->
        </div>
        <div class="search-bar">
            <input type="text" class="form-control" v-model="search" @input="fetchAsset"
                placeholder="Search Asset..." />
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Company</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Asset Code</th>
                        <th>Asset Name</th>
                        <th>Condition</th>
                        <th>Location</th>
                        <th>Checkout To</th>
                        <th>Status</th>
                        <th>Active</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(asset, index) in assets" :key="asset.RowId">
                        <td>{{ index + 1 + ((pagination.currentPage - 1) * pagination.rowsPerPage) }}</td>
                        <td>{{ asset.company.CompanyName }}</td>
                        <td>{{ asset.asset_model.brand.BrandName }}</td>
                        <td>{{ asset.asset_model.ModelName }}</td>
                        <td>{{ asset.AssetCode }}</td>
                        <td>{{ asset.AssetName }}</td>
                        <td>{{ asset.asset_condition.PlDescription }}</td>
                        <td>{{ asset.location.LocationName }}</td>
                        <td>
                            <span
                                v-text="asset.tx_checkout?.tx_checkin == null ? asset.tx_checkout?.created_person?.Nama : ''">
                            </span>
                        </td>
                        <td>
                            <span v-if="asset.Status == 'A'">Available</span>
                            <span v-else-if="asset.Status == 'CO'">Checkout</span>
                            <!-- {{ asset.Active === 1 ? 'Active' : 'Inactive' }} -->
                        </td>
                        <td>{{ asset.Active == 1 ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <button class="btn btn-primary btn-sm" @click="openShowModal(asset)">Show</button>
                                <!-- <button class="btn btn-danger btn-sm" @click="openShowModal(asset)">Re Print</button> -->
                                <router-link :to="{ name: 'asset-checkin', params: { id: asset.RowId } }">
                                    <button v-if="asset.tx_checkout" class="btn btn-success btn-sm">CheckIn</button>
                                </router-link>
                                <template v-if="!asset.tx_checkout">
                                    <button class="btn btn-info btn-sm" @click="openEditModal(asset)">Edit</button>
                                    <router-link :to="{ name: 'asset-checkout', params: { id: asset.RowId } }">
                                        <button class="btn btn-success btn-sm">CheckOut</button>
                                    </router-link>
                                </template>
                            </div>
                            <!-- <button class="btn btn-danger btn-sm" @click="deleteEmployee(location.RowId)">Delete</button> -->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <button class="btn btn-secondary" :disabled="pagination.currentPage === 1"
                @click="fetchAsset(pagination.currentPage - 1)">
                Previous
            </button>
            <span>Page {{ pagination.currentPage }} of {{ pagination.totalPages }}</span>
            <button class="btn btn-secondary" :disabled="pagination.currentPage === pagination.totalPages"
                @click="fetchAsset(pagination.currentPage + 1)">
                Next
            </button>
        </div>

        <!-- Modal Create / Update / Show -->
        <div class="modal" tabindex="-1" :class="{ show: showModal }" style="display: block;" v-if="showModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Edit Asset' : 'Add New Asset' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row">
                                <!-- Project Code (Only shown when editing) -->
                                <div class="col-md-6" v-if="isEditing || isShowing">
                                    <div class="form-group mb-3">
                                        <label for="code">Asset Code</label>
                                        <input type="text" class="form-control" :disabled="isShowing || isEditing"
                                            v-model="form.assetCode" disabled />
                                    </div>
                                </div>

                                <!-- Company -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="company">Company</label>
                                        <select id="company" class="form-control" :disabled="isShowing || isEditing"
                                            v-model="form.company">
                                            <option value="">Select Company</option>
                                            <option v-for="item in company" :value="item.CompanyId">
                                                {{ item.CompanyName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Model -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="model">Model</label>
                                        <select id="model" class="form-control" :disabled="isShowing || isEditing"
                                            v-model="form.model">
                                            <option value="">Select Model</option>
                                            <option v-for="item in model" :value="item.ModelCode">{{ item.ModelName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Condition -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="condition">Condition</label>
                                        <select id="condition" class="form-control" :disabled="isShowing"
                                            v-model="form.condition">
                                            <option value="">Select Condition</option>
                                            <option v-for="item in condition" :value="item.ChildId">
                                                {{ item.PlDescription }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Category -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="condition">Category</label>
                                        <select id="condition" class="form-control" :disabled="isShowing"
                                            v-model="form.category">
                                            <option value="">Select Condition</option>
                                            <option v-for="item in condition" :value="item.ChildId">
                                                {{ item.PlDescription }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Serial Number -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="serial_number">Serial Number</label>
                                        <input id="serial_number" placeholder="Serial Number" type="text"
                                            class="form-control" v-model="form.serialNumber" :disabled="isShowing" />
                                    </div>
                                </div>

                                <!-- Asset Name -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="asset_name">Asset Name</label>
                                        <input id="asset_name" placeholder="Asset Name" type="text" class="form-control"
                                            v-model="form.assetName" :disabled="isShowing" />
                                    </div>
                                </div>

                                <!-- Purchase Date -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="purchase_date">Purchase Date</label>
                                        <input id="purchase_date" type="date" class="form-control"
                                            v-model="form.purchaseDate" :disabled="isShowing" />
                                    </div>
                                </div>

                                <!-- Supplier -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="supplier">Supplier</label>
                                        <select id="supplier" class="form-control" v-model="form.supplier"
                                            :disabled="isShowing">
                                            <option value="">Select Supplier</option>
                                            <option v-for="item in supplier" :value="item.SupplierCode">
                                                {{ item.SupplierName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Order Number -->
                                <div class="col-md-6">
                                    <!-- <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                </div> -->
                                    <div class="form-group mb-3">
                                        <label for="order_number">Order Number</label>
                                        <input id="order_number" type="text" class="form-control"
                                            v-model="form.orderNumber" :disabled="isShowing" />
                                    </div>
                                </div>

                                <!-- Purchase Cost -->
                                <div class="col-md-6">
                                    <label for="order_number">Purchase Cost</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                        <input id="order_number" type="number" class="form-control"
                                            v-model="form.purchaseCost" :disabled="isShowing" />
                                    </div>
                                </div>

                                <!-- Warranty -->
                                <div class="col-md-6">
                                    <label for="warranty">Warranty</label>
                                    <div class="input-group mb-3">
                                        <input id="warranty" type="text" class="form-control" v-model="form.warranty"
                                            :disabled="isShowing" />
                                        <span class="input-group-text" id="basic-addon2">Month</span>
                                    </div>
                                </div>

                                <!-- Received By -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="received_by">Received By</label>
                                        <select id="received_by" class="form-control" v-model="form.receivedBy"
                                            :disabled="isShowing">
                                            <option value="">Select User</option>
                                            <option v-for="item in receivedBy" :value="item.NIK">
                                                {{ item.Nama }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="notes">Notes</label>
                                        <input id="notes" type="text" placeholder="Notes" class="form-control"
                                            v-model="form.notes" :disabled="isShowing" />
                                    </div>
                                </div>

                                <!-- Default Location -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="location">Default Location</label>
                                        <select id="location" class="form-control" v-model="form.location"
                                            :disabled="isEditing || isShowing">
                                            <option value="">Select Location</option>
                                            <option v-for="item in location" :value="item.LocationCode">
                                                {{ item.LocationName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div class="col-md-6" v-if="!isEditing && !isShowing">
                                    <div class="form-group mb-3">
                                        <label for="quantity">Quantity</label>
                                        <input placeholder="Qty" id="quantity" type="number" class="form-control"
                                            v-model="form.quantity" :disabled="isShowing" />
                                    </div>
                                </div>

                                <!-- Status (Only shown when editing) -->
                                <div class="col-md-6" v-if="isEditing">
                                    <div class="form-group mb-3">
                                        <label for="status">Status</label>
                                        <select id="status" class="form-control" v-model="form.status">
                                            <option value="A">Active</option>
                                            <option value="I">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-primary" v-show="!isShowing"
                            @click="isEditing ? updateAsset() : createAsset()">
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
import { nextTick, onMounted, ref } from "vue";

const assets = ref([])
const search = ref("")
const pagination = ref({
    currentPage: 1,
    rowsPerPage: 10,
    totalPages: 1,
})

const form = ref({
    company: '',
    model: '',
    category: '',
    condition: '',
    serialNumber: '',
    assetName: '',
    purchaseDate: '',
    supplier: '',
    orderNumber: '',
    purchaseCost: '',
    warranty: '',
    receivedBy: '',
    notes: '',
    location: '',
    quantity: '',
});

const company = ref([])
const condition = ref([])
const model = ref([])
const supplier = ref([])
const receivedBy = ref([])
const location = ref([])

const showModal = ref(false)
const isEditing = ref(false)
const isShowing = ref(false)

onMounted(() => {
    fetchAsset()
    fetchList()
})

const fetchAsset = async (page = 1) => {
    try {
        page = Number(page);

        const response = await axios.get("/api/transaksi/assets", {
            params: {
                per_page: pagination.value.rowsPerPage,
                current_page: page,
                search: search.value,
            },
        });

        const data = response.data;
        assets.value = data.data;
        console.log(form.value)
        pagination.value.currentPage = Number(data.currentPage);
        pagination.value.rowsPerPage = data.rowsPerPage;
        pagination.value.totalPages = data.totalPages;
    } catch (error) {
        console.error("Error fetching Assets:", error);
    }
}

const fetchList = async () => {
    try {
        const response = await axios.get("/api/transaksi/assets/list");

        const data = response.data.data;

        company.value = data.companies
        condition.value = data.condition
        model.value = data.model
        supplier.value = data.supplier
        receivedBy.value = data.user_received
        location.value = data.location
    } catch (error) {
        console.error("Error fetching Supported Data:", error);
    }
}

const openCreateModal = () => {

    showModal.value = true
    isEditing.value = false
}

const closeModal = () => {
    form.value = {
        company: '',
        model: '',
        category: '',
        condition: '',
        serialNumber: '',
        assetName: '',
        purchaseDate: null,
        supplier: '',
        orderNumber: '',
        purchaseCost: '',
        warranty: '',
        receivedBy: '',
        notes: '',
        location: '',
        quantity: '',
    }
    nextTick(() => {
        showModal.value = false
        isEditing.value = false
        isShowing.value = false
    });
}

const openEditModal = (item) => {
    form.value.RowId = item.RowId;
    form.value.assetCode = item.AssetCode;
    form.value.company = item.CompanyCode;
    form.value.model = item.ModelCode;
    form.value.category = item.Category;
    form.value.condition = item.Condition;
    form.value.serialNumber = item.SerialNumber;
    form.value.assetName = item.AssetName;
    form.value.purchaseDate = item.PurchaseDate;
    form.value.supplier = item.SupplierCode;
    form.value.orderNumber = item.OrderNumber;
    form.value.purchaseCost = item.PurchaseCost;
    form.value.warranty = item.Warranty;
    form.value.receivedBy = item.ReceivedBy;
    form.value.notes = item.Notes;
    form.value.location = item.LocationCode;
    form.value.quantity = item.Quantity;
    form.value.status = item.Status;

    nextTick(() => {
        showModal.value = true;
        isEditing.value = true;
    });
}

const openShowModal = (item) => {
    form.value.RowId = item.RowId;
    form.value.assetCode = item.AssetCode;
    form.value.company = item.CompanyCode;
    form.value.model = item.ModelCode;
    form.value.category = item.Category;
    form.value.condition = item.Condition;
    form.value.serialNumber = item.SerialNumber;
    form.value.assetName = item.AssetName;
    form.value.purchaseDate = item.PurchaseDate;
    form.value.supplier = item.SupplierCode;
    form.value.orderNumber = item.OrderNumber;
    form.value.purchaseCost = item.PurchaseCost;
    form.value.warranty = item.Warranty;
    form.value.receivedBy = item.ReceivedBy;
    form.value.notes = item.Notes;
    form.value.location = item.LocationCode;
    form.value.quantity = item.Quantity;
    form.value.status = item.Status;

    nextTick(() => {
        showModal.value = true;
        isShowing.value = true;
    });
}

const createAsset = async () => {
    try {
        const response = await axios.post("/api/transaksi/assets", form.value);
        alert("Asset created successfully!");

        fetchAsset();
        closeModal();
    } catch (error) {
        console.error("Error creating Asset:", error);
        alert("Failed to create Asset.");
    }
};

const updateAsset = async () => {
    try {
        const response = await axios.patch(`/api/transaksi/assets/${form.value.RowId}`, {
            assetName: form.value.assetName,
            condition: form.value.condition,
            serialNumber: form.value.serialNumber,
            category: form.value.category,
            purchaseDate: form.value.purchaseDate,
            supplier: form.value.supplier,
            orderNumber: form.value.orderNumber,
            purchaseCost: form.value.purchaseCost,
            warranty: form.value.warranty,
            notes: form.value.notes,
            receivedBy: form.value.receivedBy,
            status: form.value.status,
        });

        alert("Project updated successfully!");
        fetchAsset();
        closeModal();
    } catch (error) {
        console.error("Error updating Project:", error);
        alert("Failed to update Project.");
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
.asset-transaksi-page {
    padding: 20px;
}
</style>
