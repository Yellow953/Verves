import axios from 'axios';

// Create axios instance with default config
const api = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    withCredentials: true, // Important for Sanctum cookies
});

// Request interceptor to add auth token
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor to handle errors
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Unauthorized - clear token and redirect to login
            localStorage.removeItem('auth_token');
            // You can add redirect logic here if needed
        }
        return Promise.reject(error);
    }
);

// Auth API methods
export const authAPI = {
    register: (data) => api.post('/register', data),
    login: (data) => api.post('/login', data),
    logout: () => api.post('/logout'),
    getUser: () => api.get('/user'),
};

// Helper to set token after login
export const setAuthToken = (token) => {
    localStorage.setItem('auth_token', token);
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
};

// Helper to remove token
export const removeAuthToken = () => {
    localStorage.removeItem('auth_token');
    delete api.defaults.headers.common['Authorization'];
};

export default api;
