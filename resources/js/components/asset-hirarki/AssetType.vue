<script setup>
import { onMounted, ref } from 'vue';
import Brand from './Brand.vue';

const { id, code } = defineProps(['id', 'code'])

onMounted(() => {
    fetchAssetType()
})

const showModal = ref(false)
const isEditing = ref(false)

const assetForm = ref({
    name: '',
    alias: '',
    status: '',
    code: code,
    asset_code: '',
})

const assetType = ref([]);
const fetchAssetType = async () => {
    try {
        const response = await axios.get("/api/asset-type/" + code);
        const res = response.data;
        assetType.value = res.data;
    } catch (error) {
        console.error("Error fetching asset type:", error);
    }
}

const expandedRows = ref([]);
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
    assetForm.value.RowId = item.RowId
    assetForm.value.code = item.MainGroupCode
    assetForm.value.asset_code = item.AssetTypeCode
    assetForm.value.name = item.AssetType
    assetForm.value.alias = item.Alias
    assetForm.value.status = item.Status

    showModal.value = true;
    isEditing.value = true;
}

const createAssetType = async () => {
    try {
        const response = await axios.post("/api/asset-type", assetForm.value);
        alert("Asset Type created successfully!");

        fetchAssetType();
        closeModal();
    } catch (error) {
        console.error("Error creating asset type:", error);
        alert("Failed to create asset type.");
    }
}

const updateAssetType = async () => {
    try {
        const response = await axios.patch(`/api/asset-type/${assetForm.value.RowId}`, {
            name: assetForm.value.name,
            alias: assetForm.value.alias,
            status: assetForm.value.status,
            asset_code: assetForm.value.asset_code,
        });

        alert("Asset Type updated successfully!");
        fetchAssetType();
        closeModal();
    } catch (error) {
        console.error("Error updating asset type:", error);
        alert("Failed to update asset type.");
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

<template>
    <div class="asset-type">


        <div class="d-flex justify-content-end mb-2">
            <button class="btn btn-primary" @click="openCreateModal">Add Asset Type</button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Type Code</th>
                        <th>Type</th>
                        <th>Alias</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(item, index) in assetType" :key="item.RowId">
                        <tr>
                            <td @click.prevent="toggleExpand(item.RowId)">
                                <i :class="{ 'bi bi-plus-square': !isExpanded(item.RowId), 'bi bi-dash-square': isExpanded(item.RowId) }"
                                    style="cursor: pointer;"></i>
                            </td>
                            <td>{{ item.AssetTypeCode }}</td>
                            <td>{{ item.AssetType }}</td>
                            <td>{{ item.Alias }}</td>
                            <td>{{ item.Status === 'A' ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" @click="openEditModal(item)">Edit</button>
                                <button class="btn btn-danger btn-sm" @click="remove(item.RowId)">Delete</button>
                            </td>

                        </tr>
                        <tr>
                            <td colspan="6" v-if="isExpanded(item.RowId)">
                                <Brand :id="item.RowId" :assetCode="item.AssetTypeCode" :groupCode="code" />
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="modal" tabindex="-1" :class="{ show: showModal }" style="display: block;" v-if="showModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Edit Asset Type' : 'Add Asset Type' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3" v-if="isEditing">
                            <label for="name">Asset Type Code</label>
                            <input type="text" id="code" class="form-control" v-model="assetForm.asset_code"
                                placeholder="Asset Type Code" disabled />
                        </div>
                        <div class="form-group mb-3" v-if="!isEditing">
                            <label for="name">Main Group</label>
                            <input type="text" class="form-control" v-model="assetForm.code" disabled />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Asset Type Name</label>
                            <input type="text" id="name" class="form-control" placeholder="Asset Type Name"
                                v-model="assetForm.name" />
                        </div>
                        <div class="form-group mb-3">
                            <label for="alias">Alias</label>
                            <input type="text" id="alias" class="form-control" placeholder="Alias"
                                v-model="assetForm.alias" />
                        </div>
                        <div class="form-group" v-if="isEditing">
                            <label for="status">Status</label>
                            <select id="status" class="form-control" v-model="assetForm.status">
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-primary"
                            @click="isEditing ? updateAssetType() : createAssetType()">
                            {{ isEditing ? 'Update' : 'Submit' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.asset-type {
    padding: 20px;
}
</style>
