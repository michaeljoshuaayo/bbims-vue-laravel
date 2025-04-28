import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

// vue-auth3
import { createAuth } from 'vue-auth3';
import driverAuthBearer from 'vue-auth3/drivers/auth/bearer';
import driverHttpAxios from 'vue-auth3/drivers/http/axios';
import axiosInstance from './axios';

import Aura from '@primevue/themes/aura';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';

import '@/assets/styles.scss';
import '@/assets/tailwind.css';

const app = createApp(App);

const auth = createAuth({
    plugins: {
        router,
    },
    drivers: {
        auth: driverAuthBearer,
        http: driverHttpAxios,
    },
    http: {
        instance: axiosInstance,
    },
    rememberkey: 'auth_remember',
    tokenDefaultKey: 'auth_token_default',
    tokenImpersonateKey: 'auth_token_impersonate',
    stores: ['storage', 'cookie'],
    loginData: {
        method: 'post',
        url: 'http://127.0.0.1:8000/api/login',
        redirect: '/',
        staySignedIn: true,
        fetchUser: true,
        remember: true,
    },
    logoutData: {
        method: 'post',
        url: 'http://127.0.0.1:8000/api/logout',
        redirect: 'http://127.0.0.1:8000/api/login',
    },
    fetchData: {
        method: 'get',
        url: 'http://127.0.0.1:8000/api/user',
        enabled: true
    },
    refreshData: {
        method: 'get',
        url: 'http://127.0.0.1:8000/api/refresh',
        enabled: true,
        interval: 30,
    }
});

app.use(router);
app.use(auth);
app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            darkModeSelector: '.app-dark'
        }
    }
});
app.use(ToastService);
app.use(ConfirmationService);

app.mount('#app');
