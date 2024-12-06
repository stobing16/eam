<template>
    <div className="app-wrapper">

            <Sidebar/>

        <main className="app-main">
            <!-- App content header -->
            <div className="app-content-header">
                <div className="container-fluid">
                    <div className="row">
                        <div className="col-sm-6">
                            <h3 className="mb-0">Dashboard v2</h3>
                        </div>
                        <div className="col-sm-6">
                            <ol className="breadcrumb float-sm-end">
                                <li className="breadcrumb-item"><a href="#">Home</a></li>
                                <li className="breadcrumb-item active" aria-current="page">
                                    Dashboard v2
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- App content area where dynamic content goes -->
            <div className="app-content">
                <div className="container-fluid">
                    <router-view/> <!-- Vue Router will render the correct page here -->
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer className="app-footer">
            <strong>&copy; EAM 2024</strong>
        </footer>
    </div>
</template>

<script>
import Sidebar from './Sidebar.vue';

export default {
    name: 'App',
    components: {
        Sidebar
    }
};
</script>

<style scoped>

</style>
