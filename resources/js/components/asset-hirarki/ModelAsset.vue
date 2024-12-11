<script setup>
import { onMounted, ref } from 'vue';

const { id, assetCode, groupCode, brandCode } = defineProps(['id', 'assetCode', 'groupCode', 'brandCode'])
console.log(id, assetCode, groupCode, brandCode)

onMounted(() => {
    fetchModel()
})

const showModal = ref(false)
const isEditing = ref(false)

const modelForm = ref({
    name: '',
    status: '',
    assetCode: assetCode,
    brandCode: brandCode,
    groupCode: groupCode,
})

const models = ref([]);
const fetchModel = async () => {
    try {
        const response = await axios.get("/api/model-asset/" + brandCode);
        const res = response.data;
        models.value = res.data;
        console.log(models.value)
    } catch (error) {
        console.error("Error fetching models:", error);
    }
}

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
    modelForm.value.RowId = item.RowId
    modelForm.value.groupCode = item.MainGroupCode
    modelForm.value.assetCode = item.AssetTypeCode
    modelForm.value.brandCode = item.BrandCode
    modelForm.value.code = item.ModelCode
    modelForm.value.name = item.ModelName
    modelForm.value.status = item.Status

    showModal.value = true;
    isEditing.value = true;
}

const createModel = async () => {
    try {
        const response = await axios.post("/api/model-asset", modelForm.value);
        alert("Model Asset created successfully!");

        fetchModel();
        closeModal();
    } catch (error) {
        console.error("Error creating model asset:", error);
        alert("Failed to create model asset.");
    }
}

const updateModel = async () => {
    try {
        const response = await axios.patch(`/api/model-asset/${modelForm.value.RowId}`, {
            name: modelForm.value.name,
            status: modelForm.value.status,
            assetCode: modelForm.value.assetCode,
            groupCode: modelForm.value.groupCode,
            brandCode: modelForm.value.groupCode,
        });

        alert("Brand updated successfully!");
        fetchModel();
        closeModal();
    } catch (error) {
        console.error("Error updating brand:", error);
        alert("Failed to update brand.");
    }
}

const remove = async (id) => {
    const confirmDelete = confirm("Are you sure you want to delete this model asset?");
    if (confirmDelete) {
        axios.delete(`/api/model-asset/${id}`)
            .then((response) => {
                if (response.data.success) {
                    alert("Model Asset deleted successfully!");
                    fetchModel();
                } else {
                    alert("Failed to model asset brand.");
                }
            })
            .catch((error) => {
                console.error("Error deleting model asset:", error);
                alert("An error occurred while deleting the model asset.");
            });
    }
}

</script>

<template>
    <div class="brand">
        <div class="d-flex justify-content-end mb-2">
            <button class="btn btn-primary" @click="openCreateModal">Add Model</button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Model Code</th>
                        <th>Model</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(item, index) in models" :key="item.RowId">
                        <tr>
                            <td>#</td>
                            <td>{{ item.ModelCode }}</td>
                            <td>{{ item.ModelName }}</td>
                            <td>{{ item.Status === 'A' ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" @click="openEditModal(item)">Edit</button>
                                <button class="btn btn-danger btn-sm" @click="remove(item.RowId)">Delete</button>
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
                        <h5 class="modal-title">{{ isEditing ? 'Edit Model Asset' : 'Add Model Asset' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name">Main Group Code</label>
                            <input type="text" class="form-control" v-model="modelForm.groupCode" disabled />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Asset Type Code</label>
                            <input type="text" class="form-control" v-model="modelForm.assetCode" disabled />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Brand Code</label>
                            <input type="text" id="code" class="form-control" v-model="modelForm.brandCode"
                                placeholder="Asset Type Code" disabled />
                        </div>
                        <div class="form-group mb-3" v-if="isEditing">
                            <label for="name">Model Code</label>
                            <input type="text" id="model-code" class="form-control" v-model="modelForm.code"
                                placeholder="Model Code" disabled />
                        </div>
                        <div class="form-group mb-3">
                            <label for="name">Model Name</label>
                            <input type="text" id="name" class="form-control" placeholder="Model Name"
                                v-model="modelForm.name" />
                        </div>
                        <div class="form-group" v-if="isEditing">
                            <label for="status">Status</label>
                            <select id="status" class="form-control" v-model="modelForm.status">
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-primary"
                            @click="isEditing ? updateModel() : createModel()">
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
