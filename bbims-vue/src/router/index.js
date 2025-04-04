import AppLayout from '@/layout/AppLayout.vue';
import { createRouter, createWebHistory } from 'vue-router';


const routes = [
    {
        path: '/',
        component: AppLayout,
        children: [
            {
                path: '/',
                name: 'dashboard',
                component: () => import('@/views/Dashboard.vue'),
                meta: { auth: true, roles: ['admin', 'user', 'staff'] }
            },
            {
                path: '/menu/RIS-Form',
                name: 'RISForm',
                component: () => import('@/views/menu/RISForm.vue'),
                meta: { auth: true , roles: ['user'] }
            },
            {
                path: '/menu/blood-request-list',
                name: 'table',
                component: () => import('@/views/menu/BloodRequestList.vue'),
                meta: { auth: true, roles: ['admin', 'staff'] }
            },
            {
                path: '/menu/charts',
                name: 'charts',
                component: () => import('@/views/menu/ChartDoc.vue'),
                meta: { auth: true, roles: ['admin'] }
            },
            {
                path: '/menu/timeline',
                name: 'timeline',
                component: () => import('@/views/menu/BloodRequestTimeline.vue'),
                meta: { auth: true, roles: ['user'] }
            },
            {   
                path: 'pages/blood-inventory',
                name: 'blood-inventory',
                component: () => import('@/views/pages/BloodInventory.vue'),
                meta: { auth: true, roles: ['admin', 'staff']  }
            },
            {   
                path: 'menu/user-list',
                name: 'user-list',
                component: () => import('@/views/menu/UserList.vue'),
                meta: { auth: true, roles: ['admin']  }
            },
            {
                path: 'menu/usage-history',
                name: 'usage-history',
                component: () => import('@/views/menu/UsageHistory.vue'),
                meta: { auth: true, roles: ['admin', 'staff'] }
            },
            {
                path: 'menu/forecasting',
                name: 'forecasting',
                component: () => import('@/views/menu/Forecasting.vue'),
                meta: { auth: true, roles: ['admin', 'staff'] }
            },
            {
                path: 'pages/expired-blood',
                name: 'expired-blood',
                component: () => import('@/views/pages/ExpiredBlood.vue'),
                meta: { auth: true, roles: ['admin', 'staff'] }
            },
            {
                path: 'menu/request-history',
                name: 'request-history',
                component: () => import('@/views/menu/RequestHistory.vue'),
                meta: { auth: true, roles: ['user'] }
            },
            {
                path: 'menu/sarima',
                name: 'sarima',
                component: () => import('@/views/menu/Sarima.vue'),
                meta: { auth: true, roles: ['admin', 'staff'] }
            }


        ]
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/pages/auth/Login.vue'),
        meta: { auth: false }
    },
    {
        path: '/404',
        name: '404',
        component: () => import('@/views/pages/auth/Error.vue'),
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes
});


export default router;
