import AppLayout from '@/layout/AppLayout.vue';
import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from 'vue-auth3';


const routes = [
    {
        path: '/',
        component: AppLayout,
        children: [
            {
                path: '/',
                name: 'dashboard',
                component: () => import('@/views/Dashboard.vue'),
                meta: { auth: true  }
            },
            {
                path: '/menu/RIS-Form',
                name: 'RISForm',
                component: () => import('@/views/menu/RISForm.vue'),
                meta: { auth: true  }
            },
            {
                path: '/menu/blood-requests',
                name: 'table',
                component: () => import('@/views/menu/BloodRequestList.vue'),
                meta: { auth: true  }
            },
            {
                path: '/menu/charts',
                name: 'charts',
                component: () => import('@/views/menu/ChartDoc.vue'),
                meta: { auth: true  }
            },
            {
                path: '/menu/timeline',
                name: 'timeline',
                component: () => import('@/views/menu/BloodRequestTimeline.vue'),
                meta: { auth: true  }
            },
            {   
                path: 'pages/blood-inventory',
                name: 'blood-inventory',
                component: () => import('@/views/pages/BloodInventory.vue'),
                meta: { auth: true  }
            },
            {   
                path: 'menu/user-list',
                name: 'user-list',
                component: () => import('@/views/menu/UserList.vue'),
                meta: { auth: true  }
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
