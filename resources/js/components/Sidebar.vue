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
                    <!-- <li class="nav-item">
                        <router-link to="/employee" class="nav-link">
                            <i class="bi bi-people-fill"></i>
                            <p>Employee</p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/asset-hirarki" class="nav-link">
                            <i class="bi bi-people-fill"></i>
                            <p>Asset Hirarki</p>
                        </router-link>
                    </li> -->


                    <li class="nav-item" v-for="(item, index) in dropdownItems" :key="index">
                        <a href="#" class="nav-link" @click.prevent="toggleDropdown(index)">
                            <i class="bi bi-gear"></i>
                            <p>{{ item.title }}</p>
                            <i :class="dropdownOpen[index] ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
                        </a>
                        <ul v-show="dropdownOpen[index]" class="nav flex-column ms-3">
                            <li class="nav-item" v-for="(link, index) in item.menu" :key="index">
                                <router-link :to="link.route" class="nav-link">{{ link.name }}</router-link>
                            </li>
                            <!-- <li class="nav-item">
                                <router-link to="/sub-item-2" class="nav-link">Sub Item 2</router-link>
                            </li> -->
                            <!-- Add more sub-items as needed -->
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</template>

<script setup>
import { ref } from 'vue';

const dropdownItems = [
    {
        title: 'Master',
        icon: 'icon',
        menu: [
            {
                route: '/employee',
                name: 'Employee',
            },
            {
                route: '/asset-hirarki',
                name: 'Asset Hirarki',
            },
        ]
    },
    // { title: 'Reports' },
    // Add more dropdown menu items as needed
];
const dropdownOpen = ref([]);
const toggleDropdown = (index) => {
    dropdownOpen.value[index] = !dropdownOpen.value[index];
};
</script>

<style scoped></style>
