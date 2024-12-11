<script setup>
import { onMounted, ref } from 'vue';
import ModelAsset from './ModelAsset.vue';

const { id, assetCode, groupCode } = defineProps(['id', 'assetCode', 'groupCode'])

onMounted(() => {
    fetchBrand()
})

const showModal = ref(false)
const isEditing = ref(false)

const brandForm = ref({
    name: '',
    status: '',
    assetCode: assetCode,
    groupCode: groupCode,
})

const brands = ref([]);
const fetchBrand = async () => {
    try {
        const response = await axios.get("/api/brand/" + assetCode);
        const res = response.data;
        brands.value = res.data;
    } catch (error) {
        console.error("Error fetching brand:", error);
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
    brandForm.value.RowId = item.RowId
    brandForm.value.groupCode = item.MainGroupCode
    brandForm.value.assetCode = item.AssetTypeCode
    brandForm.value.code = item.BrandCode
    brandForm.value.name = item.BrandName
    brandForm.value.status = item.Status

    showModal.value = true;
    isEditing.value = true;
}

const createBrand = async () => {
    try {
        const response = await axios.post("/api/brand", brandForm.value);
        alert("Brand created successfully!");

        fetchBrand();
        closeModal();
    } catch (error) {
        console.error("Error creating brand:", error);
        alert("Failed to create brand.");
    }
}

const updateBrand = async () => {
    try {
        const response = await axios.patch(`/api/brand/${brandForm.value.RowId}`, {
            name: brandForm.value.name,
            status: brandForm.value.status,
            assetCode: brandForm.value.assetCode,
            groupCode: brandForm.value.groupCode,
        });

        alert("Brand updated successfully!");
        fetchBrand();
        closeModal();
    } catch (error) {
        console.error("Error updating brand:", error);
        alert("Failed to update brand.");
    }
}

const remove = async (id) => {
    const confirmDelete = confirm("Are you sure you want to delete this brand?");
    if (confirmDelete) {
        axios.delete(`/api/brand/${id}`)
            .then((response) => {
                if (response.data.success) {
                    alert("Brand deleted successfully!");
                    fetchBrand();
                } else {
                    alert("Failed to delete brand.");
                }
            })
            .catch((error) => {
                console.error("Error deleting brand:", error);
                alert("An error occurred while deleting the brand.");
            });
    }
}

</script>

<template>
    <div class="brand">
        <div class="d-flex justify-content-end mb-2">
            <button class="btn btn-primary" @click="openCreateModal">Add Brand</button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Brand Code</th>
                        <th>Brand</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(item, index) in brands" :key="item.RowId">
                        <tr>
                            <td @click.prevent="toggleExpand(item.RowId)">
                                <i :class="{ 'bi bi-plus-square': !isExpanded(item.RowId), 'bi bi-dash-square': isExpanded(item.RowId) }"
                                    style="cursor: pointer;"></i>
                            </td>
                            <td>{{ item.BrandCode }}</td>
                            <td>{{ item.BrandName }}</td>
                            <td>{{ item.Status === 'A' ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" @click="openEditModal(item)">Edit</button>
                                <button class="btn btn-danger btn-sm" @click="remove(item.RowId)">Delete</button>
                            </td>

                        </tr>
                        <tr>
                            <td colspan="5" v-if="isExpanded(item.RowId)">
                                <ModelAsset :id="item.RowId" :brandCode="item.BrandCode" :assetCode="item.AssetTypeCode"
                                    :groupCode="item.MainGroupCode" />
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
                        <h5 class="modal-title">{{ isEditing ? 'Edit Brand' : 'Add Brand' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name">Main Group Code</label>
                            <input type="text" class="form-control" v-model="brandForm.groupCode" disabled />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Asset Type Code</label>
                            <input type="text" class="form-control" v-model="brandForm.assetCode" disabled />
                        </div>
                        <div class="form-group mb-3" v-if="isEditing">
                            <label for="name">Brand Code</label>
                            <input type="text" id="code" class="form-control" v-model="brandForm.code"
                                placeholder="Asset Type Code" disabled />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Brand Name</label>
                            <input type="text" id="name" class="form-control" placeholder="Brand Name"
                                v-model="brandForm.name" />
                        </div>
                        <div class="form-group" v-if="isEditing">
                            <label for="status">Status</label>
                            <select id="status" class="form-control" v-model="brandForm.status">
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-primary"
                            @click="isEditing ? updateBrand() : createBrand()">
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
