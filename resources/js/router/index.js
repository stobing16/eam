import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '../layouts/AppLayout.vue';
import AuthLayout from '../layouts/AuthLayout.vue';

// const isLoggedIn = () => localStorage.getItem('token');

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
        path: '/master',
        children: [
            {
                path: 'employee',
                meta: {
                    layout: AppLayout,
                    requiresAuth: true
                },
                name: 'employee',
                component: () => import('../views/master/Employee.vue')
            },
            {
                path: 'company',
                meta: {
                    layout: AppLayout,
                    requiresAuth: true
                },
                name: 'company',
                component: () => import('../views/master/Company.vue')
            },
            {
                path: 'supplier',
                meta: {
                    layout: AppLayout,
                    requiresAuth: true
                },
                name: 'supplier',
                component: () => import('../views/master/Supplier.vue')
            },
            {
                path: 'location',
                meta: {
                    layout: AppLayout,
                    requiresAuth: true
                },
                name: 'location',
                component: () => import('../views/master/Location.vue')
            },
            {
                path: 'sub-location',
                meta: {
                    layout: AppLayout,
                    requiresAuth: true
                },
                name: 'sub-location',
                component: () => import('../views/master/SubLocation.vue')
            },
            {
                path: 'project',
                meta: {
                    layout: AppLayout,
                    requiresAuth: true
                },
                name: 'project',
                component: () => import('../views/master/Project.vue')
            },
            {
                path: 'asset-hirarki',
                meta: {
                    layout: AppLayout,
                    requiresAuth: true
                },
                name: 'asset-hirarki',
                component: () => import('../views/master/AssetHirarki.vue')
            },
        ]
    },
    {
        path: '/transaksi',
        children: [
            {
                path: 'asset',
                meta: {
                    layout: AppLayout,
                    requiresAuth: true
                },
                name: 'asset',
                component: () => import('../views/transaksi/Asset.vue')
            },
        ]
    },
    {
        path: '/report',
        children: [
            {
                path: 'barcode-collecting',
                meta: {
                    layout: AppLayout,
                    requiresAuth: true
                },
                name: 'barcode-collecting',
                component: () => import('../views/report/BarcodeCollecting.vue')
            },
        ]
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

// router.beforeEach((to, from, next) => {
//     if (to.meta.requiresAuth && !isLoggedIn()) {
//         next({ name: 'login' });
//     } else {
//         next();
//     }
// });

export default router;
