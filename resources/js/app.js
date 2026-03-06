import 'bootstrap';
import axios from 'axios';
window.axios = axios;

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

window.axios.defaults.baseURL = '/';

const authToken = localStorage.getItem('auth_token');
if (authToken) {
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + authToken;
}