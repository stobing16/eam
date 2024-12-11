import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '../layouts/AppLayout.vue';
import AuthLayout from '../layouts/AuthLayout.vue';

const isLoggedIn = () => localStorage.getItem('token');

const routes = [
    {
        path: '/',
        meta: {
            layout: AppLayout,
            requiresAuth: true
        },
        name: 'dashboard',
        component: () => import('../views/Dashboard.vue')
    },
    {
        path: '/employee',
        meta: {
            layout: AppLayout,
            requiresAuth: true
        },
        name: 'employee',
        component: () => import('../views/master/Employee.vue')
    },
    {
        path: '/asset-hirarki',
        meta: {
            layout: AppLayout,
            requiresAuth: true
        },
        name: 'asset-hirarki',
        component: () => import('../views/master/AssetHirarki.vue')
    },
    {
        path: '/login',
        meta: { layout: AuthLayout },
        name: 'login',
        component: () => import('../views/auth/Login.vue')
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !isLoggedIn()) {
        next({ name: 'login' });
    } else {
        next();
    }
});

export default router;
