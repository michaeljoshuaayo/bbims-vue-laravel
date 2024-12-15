import AppLayout from '@/layout/AppLayout.vue';
import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: AppLayout,
            children: [
                {
                    path: '/',
                    name: 'dashboard',
                    component: () => import('@/views/Dashboard.vue')
                },
                {
                    path: '/menu/RIS-Form',
                    name: 'RISForm',
                    component: () => import('@/views/menu/RISForm.vue')
                },
                {
                    path: '/menu/blood-requests',
                    name: 'table',
                    component: () => import('@/views/menu/BloodRequestList.vue')
                },
                {
                    path: '/menu/charts',
                    name: 'charts',
                    component: () => import('@/views/menu/ChartDoc.vue')
                },
                {
                    path: '/menu/timeline',
                    name: 'timeline',
                    component: () => import('@/views/menu/BloodRequestTimeline.vue')
                },
                {
                    path: '/pages/blood-inventory',
                    name: 'bloodinventory',
                    component: () => import('@/views/pages/BloodInventory.vue')
                },
                {
                    path: '/menu/user-list',
                    name: 'userlist',
                    component: () => import('@/views/menu/UserList.vue')
                }
            ]
        },
        {
            path: '/landing',
            name: 'landing',
            component: () => import('@/views/pages/Landing.vue')
        },
        {
            path: '/pages/notfound',
            name: 'notfound',
            component: () => import('@/views/pages/NotFound.vue')
        },

        {
            path: '/auth/login',
            name: 'login',
            component: () => import('@/views/pages/auth/Login.vue')
        },
        {
            path: '/auth/access',
            name: 'accessDenied',
            component: () => import('@/views/pages/auth/Access.vue')
        },
        {
            path: '/auth/error',
            name: 'error',
            component: () => import('@/views/pages/auth/Error.vue')
        }
    ]
});

export default router;
