<template>
    <div class="app-wrapper">
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">
                    <router-view />
                </div>
            </div>
        </main>
    </div>
</template>

<script>
export default {
    name: 'AuthLayout'
};
</script>

<style scoped></style>
