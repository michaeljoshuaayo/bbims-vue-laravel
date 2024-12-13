// src/axios.js
import axios from 'axios';
import Cookies from 'js-cookie';

const axiosInstance = axios.create({
    baseURL: 'http://127.0.0.1:8000/api', // Use the specified base URL
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        Accept: 'application/json'
    },
    withCredentials: true // Include credentials for cross-origin requests
});

// Add a request interceptor to include the CSRF token
axiosInstance.interceptors.request.use(config => {
    const token = Cookies.get('XSRF-TOKEN');
    if (token) {
        config.headers['X-XSRF-TOKEN'] = token;
    }
    return config;
});

export default axiosInstance;
