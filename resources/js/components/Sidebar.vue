<template>
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!-- Sidebar Brand -->
        <div class="sidebar-brand">
            <router-link to="/" class="brand-link">
                <img src="/assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow">
                <span class="brand-text fw-light">EAM</span>
            </router-link>
        </div>

        <!-- Sidebar Wrapper -->
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <!-- Sidebar Menu -->
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                    <li class="nav-item" v-for="(item, index) in dropdownItems" :key="index">
                        <a href="#" class="nav-link" @click.prevent="toggleDropdown(index)">
                            <div class="d-flex justify-content-between w-100">
                                <div>
                                    <i class="bi" :class="[item.icon ? item.icon : 'bi-gear']"></i>
                                    <p>{{ item.title }}</p>
                                </div>
                                <i :class="dropdownOpen[index] ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
                            </div>
                        </a>
                        <ul v-show="dropdownOpen[index]" class="nav flex-column ms-3">
                            <li class="nav-item" v-for="(link, index) in item.menu" :key="index">
                                <router-link :to="link.route" class="nav-link"
                                    :class="{ 'active-link': isActiveRoute(link.route) }">{{ link.name }}</router-link>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</template>

<script setup>
import { ref } from 'vue';
import { useRoute } from 'vue-router';

// Data untuk dropdown items
const dropdownItems = [
    {
        title: 'Master',
        icon: 'bi-database-fill',
        menu: [
            { route: '/master/company', name: 'Company' },
            { route: '/master/employee', name: 'Employee' },
            { route: '/master/asset-hirarki', name: 'Asset Hirarki' },
            { route: '/master/supplier', name: 'Supplier' },
            { route: '/master/location', name: 'Location' },
            { route: '/master/sub-location', name: 'Sub Location' },
            { route: '/master/project', name: 'Project' },
        ]
    },
    {
        title: 'Transaksi',
        icon: '',
        menu: [
            { route: '/transaksi/asset', name: 'Asset' },
        ]
    }
];

// State untuk dropdown terbuka
const dropdownOpen = ref([]);

// Fungsi untuk toggle dropdown
const toggleDropdown = (index) => {
    dropdownOpen.value[index] = !dropdownOpen.value[index];
};

// Menggunakan Vue Router untuk mendapatkan route saat ini
const route = useRoute();

// Fungsi untuk memeriksa apakah rute aktif
const isActiveRoute = (routeToCheck) => {
    return route.path === routeToCheck;
}
</script>

<style scoped>
/* Gaya untuk item menu yang aktif */
.active-link {
    background-color: #007bff;
    color: white;
}

.active-link:hover {
    background-color: #0056b3;
}
</style>
