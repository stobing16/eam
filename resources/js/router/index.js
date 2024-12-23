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
                children: [
                    {
                        path: '',
                        meta: {
                            layout: AppLayout,
                            requiresAuth: true
                        },
                        name: 'asset',
                        component: () => import('../views/transaksi/Asset.vue'),
                    },
                    {
                        path: 'checkout/:id',
                        meta: {
                            layout: AppLayout,
                            requiresAuth: true
                        },
                        props: true,
                        name: 'asset-checkout',
                        component: () => import('../views/transaksi/AssetCheckout.vue'),
                    },
                    {
                        path: 'checkin/:id',
                        meta: {
                            layout: AppLayout,
                            requiresAuth: true
                        },
                        props: true,
                        name: 'asset-checkin',
                        component: () => import('../views/transaksi/AssetCheckIn.vue'),
                    }
                ],
            },
            {
                path: 'opname-order',
                children: [
                    {
                        path: '',
                        meta: {
                            layout: AppLayout,
                            requiresAuth: true
                        },
                        name: 'opname-order',
                        component: () => import('../views/transaksi/OpnameOrder.vue'),
                    },
                    {
                        path: ':id',
                        meta: {
                            layout: AppLayout,
                            requiresAuth: true
                        },
                        props: true,
                        name: 'opname-order-details',
                        component: () => import('../views/transaksi/OpnameOrderDetails.vue'),
                    },
                ],
            },
            {
                path: 'asset-movement',
                children: [
                    {
                        path: '',
                        meta: {
                            layout: AppLayout,
                            requiresAuth: true
                        },
                        name: 'asset-movement',
                        component: () => import('../views/transaksi/AssetMovement.vue'),
                    },
                    // {
                    //     path: ':id',
                    //     meta: {
                    //         layout: AppLayout,
                    //         requiresAuth: true
                    //     },
                    //     props: true,
                    //     name: 'opname-order-details',
                    //     component: () => import('../views/transaksi/OpnameOrderDetails.vue'),
                    // },
                ],
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
            {
                path: 'asset-log-history',
                meta: {
                    layout: AppLayout,
                    requiresAuth: true
                },
                name: 'asset-log-history',
                component: () => import('../views/report/AssetLogHistory.vue')
            },
            {
                path: 'barcode-printing',
                meta: {
                    layout: AppLayout,
                    requiresAuth: true
                },
                name: 'barcode-printing',
                component: () => import('../views/report/BarcodePrinting.vue')
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
