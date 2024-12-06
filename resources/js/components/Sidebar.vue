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
                    <li class="nav-item">
                        <router-link to="/employee" class="nav-link">
                            <i class="bi bi-people-fill"></i>
                            <p>Employee</p>
                        </router-link>
                    </li>
                    <!-- Add more menu items as needed -->
                </ul>
            </nav>
        </div>
    </aside>
</template>

<script>
export default {
    name: 'Sidebar',
};
</script>

<style scoped>

</style>
