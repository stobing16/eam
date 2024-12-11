<template>
    <component :is="layoutComponent">
        <router-view /> <!-- Konten halaman akan dirender di sini -->
    </component>
</template>

<script>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

export default {
    name: 'App',
    setup() {
        const route = useRoute();

        const layoutComponent = computed(() => {
            return route.meta.layout || 'div';
        });

        return {
            layoutComponent
        };
    }
};
</script>

<style scoped></style>
