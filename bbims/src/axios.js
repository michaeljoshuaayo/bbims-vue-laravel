// src/axios.js
import axios from 'axios';

const axiosInstance = axios.create({
    baseURL: 'http://your-laravel-backend-url/api', // Replace with your Laravel backend URL
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json'
    }
});
export default axiosInstance;
