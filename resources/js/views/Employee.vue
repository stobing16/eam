<template>
    <div class="employee-page">
        <h1 class="page-title">Employee List</h1>
        <div class="search-bar">
            <input
                type="text"
                class="form-control"
                v-model="search"
                @input="fetchEmployees"
                placeholder="Search employees..."
            />
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(employee, index) in employees" :key="employee.id">
                    <td>{{ index + 1 + (pagination.currentPage - 1) * pagination.rowsPerPage }}</td>
                    <td>{{ employee.Nama }}</td>
                    <td>{{ employee.Department }}</td>
                    <td>{{ employee.Jabatan }}</td>
                    <td>{{ employee.Email }}</td>
                    <td>{{ employee.Status === 'A' ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <button class="btn btn-info btn-sm" @click="openEditModal(employee)">Edit</button>
                        <button class="btn btn-danger btn-sm" @click="deleteEmployee(employee.RowId)">Delete</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <button
                class="btn btn-secondary"
                :disabled="pagination.currentPage === 1"
                @click="fetchEmployees(pagination.currentPage - 1)"
            >
                Previous
            </button>
            <span>Page {{ pagination.currentPage }} of {{ pagination.totalPages }}</span>
            <button
                class="btn btn-secondary"
                :disabled="pagination.currentPage === pagination.totalPages"
                @click="fetchEmployees(pagination.currentPage + 1)"
            >
                Next
            </button>
        </div>


        <button class="btn btn-primary" @click="openCreateModal">Create Employee</button>


        <div class="modal" tabindex="-1" :class="{ show: showModal }" style="display: block;" v-if="showModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Edit Employee' : 'Create Employee' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input
                                type="text"
                                id="name"
                                class="form-control"
                                v-model="selectedEmployee.Nama"
                            />
                        </div>
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input
                                type="text"
                                id="department"
                                class="form-control"
                                v-model="selectedEmployee.Department"
                            />
                        </div>
                        <div class="form-group">
                            <label for="jabatan">Position</label>
                            <select
                                id="jabatan"
                                class="form-control"
                                v-model="selectedEmployee.Jabatan"
                            >
                                <option value="">Select Jabatan</option>
                                <option v-for="jabatan in jabatanList" :key="jabatan.id" :value="jabatan.Jabatan">
                                    {{ jabatan.Jabatan }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input
                                type="email"
                                id="email"
                                class="form-control"
                                v-model="selectedEmployee.Email"
                            />
                        </div>

                        <div class="form-group" v-if="isEditing">
                            <label for="status">Status</label>
                            <select
                                id="status"
                                class="form-control"
                                v-model="selectedEmployee.Status"
                            >
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="isEditing ? updateEmployee() : createEmployee()">
                            {{ isEditing ? 'Save Changes' : 'Create Employee' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Employee",
    data() {
        return {
            employees: [],
            search: "",
            pagination: {
                currentPage: 1,
                rowsPerPage: 10,
                totalPages: 1,
            },
            showModal: false,
            selectedEmployee: {
                Nama: "",
                Department: "",
                Jabatan: "",
                Email: "",
                Status: "A",
            },
            isEditing: false,
        };
    },
    mounted() {
        this.fetchEmployees();
        this.fetchJabatan();
    },
    methods: {
        async fetchEmployees(page = 1) {
            try {
                page = Number(page);

                const response = await axios.get("/api/employee", {
                    params: {
                        per_page: this.pagination.rowsPerPage,
                        current_page: page,
                        search: this.search,
                    },
                });

                const data = response.data;
                this.employees = data.data;
                this.pagination.currentPage = Number(data.currentPage); // Ensure it's a number
                this.pagination.rowsPerPage = data.rowsPerPage;
                this.pagination.totalPages = data.totalPages;
            } catch (error) {
                console.error("Error fetching employees:", error);
            }
        },
        async fetchJabatan() {
            try {
                const response = await axios.get("/api/employee/jabatan");
                this.jabatanList = response.data.data;
            } catch (error) {
                console.error("Error fetching jabatan data:", error);
            }
        },

        openEditModal(employee) {
            this.selectedEmployee = { ...employee };
            if (!this.selectedEmployee.Status) {
                this.selectedEmployee.Status = "A";
            }
            this.showModal = true;
            this.isEditing = true;
            this.showModal = true;
        },

        openCreateModal() {
            this.selectedEmployee = {
                Nama: "",
                Department: "",
                Jabatan: "",
                Email: "",
                Status: "A",
            };
            this.isEditing = false;
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.selectedEmployee = {};
        },

        async createEmployee() {
            try {
                const response = await axios.post("/api/employee", this.selectedEmployee);
                alert("Employee created successfully!");
                this.fetchEmployees(this.pagination.currentPage);
                this.closeModal();
            } catch (error) {
                console.error("Error creating employee:", error);
                alert("Failed to create employee.");
            }
        },

        async updateEmployee() {
            try {
                const response = await axios.patch(`/api/employee/${this.selectedEmployee.RowId}`, {
                    Nama: this.selectedEmployee.Nama,
                    Department: this.selectedEmployee.Department,
                    Jabatan: this.selectedEmployee.Jabatan,
                    Email: this.selectedEmployee.Email,
                    Status: this.selectedEmployee.Status,
                });

                alert("Employee updated successfully!");
                this.fetchEmployees(this.pagination.currentPage);
                this.closeModal();
            } catch (error) {
                console.error("Error updating employee:", error);
                alert("Failed to update employee.");
            }
        },


        deleteEmployee(id) {
            const confirmDelete = confirm("Are you sure you want to delete this employee?");
            if (confirmDelete) {
                axios
                    .delete(`/api/employee/${id}`)
                    .then((response) => {
                        if (response.data.success) {
                            alert("Employee deleted successfully!");
                            this.fetchEmployees(this.pagination.currentPage);
                        } else {
                            alert("Failed to delete employee.");
                        }
                    })
                    .catch((error) => {
                        console.error("Error deleting employee:", error);
                        alert("An error occurred while deleting the employee.");
                    });
            }
        },
    },
};
</script>

<style scoped>
.employee-page {
    padding: 20px;
}

.page-title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}

.search-bar {
    margin-bottom: 20px;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th,
.table td {
    text-align: left;
    padding: 10px;
    border: 1px solid #dee2e6;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    margin-right: 5px;
}

.modal-enter-active, .modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter, .modal-leave-to /* .modal-leave-active in <2.1.8 */ {
    opacity: 0;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 20px;
    border-radius: 8px;
    width: 100%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transform: scale(0.8);
    transition: transform 0.3s ease-in-out;
}

/* Show the modal */
.modal.show {
    display: block;
    opacity: 1;
}

.modal.show .modal-content {
    transform: scale(1);
}

.close-btn {
    color: red;
    float: right;
    font-size: 24px;
    cursor: pointer;
}

</style>
