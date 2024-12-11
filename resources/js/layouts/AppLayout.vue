<template>
    <div className="app-wrapper">
        <Sidebar />
        <main class="app-main">
            <!-- App content header -->
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Dashboard v2</h3>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-end gap-4 align-items-center">
                                <ol class="breadcrumb float-sm-end">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Dashboard v2
                                    </li>
                                </ol>
                                <button @click.prevent="logout" type="button" class="btn btn-primary">Logout</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- App content area where dynamic content goes -->
            <div class="app-content">
                <div class="container-fluid">
                    <router-view />
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="app-footer">
            <strong>&copy; EAM 2024</strong>
        </footer>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import Sidebar from '../components/Sidebar.vue';

const router = useRouter()

const logout = async () => {
    localStorage.removeItem('token');
    document.cookie = 'refresh_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;';

    await axios.post('/api/logout')
        .then(response => {
            router.push({ name: 'login' });
        })
        .catch(error => {
            console.error(error);
        });

    delete axios.defaults.headers.common['Authorization'];
    router.push({ name: 'login' });
};

</script>

<style>
i:hover {
    -webkit-text-stroke: 1px;
    cursor: pointer;
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

.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter,
.modal-leave-to

/* .modal-leave-active in <2.1.8 */
    {
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
