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

// Coaches API methods (public)
export const coachesAPI = {
    list: (params = {}) => api.get('/coaches', { params }),
    get: (id) => api.get(`/coaches/${id}`),
    getAvailableSlots: (id, params = {}) => api.get(`/coaches/${id}/available-slots`, { params }),
};

// Bookings API methods (requires auth)
export const bookingsAPI = {
    list: (params = {}) => api.get('/coach/bookings', { params }),
    create: (data) => api.post('/coach/bookings', data),
    get: (id) => api.get(`/coach/bookings/${id}`),
    update: (id, data) => api.put(`/coach/bookings/${id}`, data),
    cancel: (id, reason) => api.post(`/coach/bookings/${id}/cancel`, { reason }),
    delete: (id) => api.delete(`/coach/bookings/${id}`),
};

// Exercises API methods (public for coaches)
export const exercisesAPI = {
    list: (params = {}) => api.get('/exercises', { params }),
    get: (id) => api.get(`/exercises/${id}`),
    getMuscleGroups: () => api.get('/exercises/muscle-groups'),
    getEquipmentTypes: () => api.get('/exercises/equipment-types'),
};

// Coach-Client Relationships API
export const relationshipsAPI = {
    list: (params = {}) => api.get('/coach/relationships', { params }),
    create: (data) => api.post('/coach/relationships', data),
    get: (id) => api.get(`/coach/relationships/${id}`),
    update: (id, data) => api.put(`/coach/relationships/${id}`, data),
    delete: (id) => api.delete(`/coach/relationships/${id}`),
};

// Programs API
export const programsAPI = {
    list: (params = {}) => api.get('/coach/programs', { params }),
    create: (data) => api.post('/coach/programs', data),
    get: (id) => api.get(`/coach/programs/${id}`),
    update: (id, data) => api.put(`/coach/programs/${id}`, data),
    delete: (id) => api.delete(`/coach/programs/${id}`),
};

// Program Exercises API
export const programExercisesAPI = {
    list: (programId, params = {}) => api.get(`/coach/programs/${programId}/exercises`, { params }),
    create: (programId, data) => api.post(`/coach/programs/${programId}/exercises`, data),
    update: (programId, id, data) => api.put(`/coach/programs/${programId}/exercises/${id}`, data),
    delete: (programId, id) => api.delete(`/coach/programs/${programId}/exercises/${id}`),
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
