import { createRouter, createWebHistory } from 'vue-router';
import Employee from '../views/Employee.vue';

const routes = [
    {
        path: '/',
        name: 'Dashboard',
        component: () => import('../views/Dashboard.vue')
    },
    {
        path: '/employee',
        name: 'Employee',
        component: Employee
    }
    // Define more routes as needed
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
