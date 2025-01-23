import axios from "axios";

// Public Axios instance (non-authenticated requests)
const publicAxios = axios.create({
    baseURL: "/api",
    headers: {
        "X-Requested-With": "XMLHttpRequest",
    },
});

// Authenticated Axios instance
const authAxios = axios.create({
    baseURL: "/api",
    headers: {
        "X-Requested-With": "XMLHttpRequest",
    },
});

// Add a request interceptor to authAxios to include the token
authAxios.interceptors.request.use((config) => {
    const token = localStorage.getItem("token");
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, (error) => {
    return Promise.reject(error); // Propagate other errors
});

authAxios.interceptors.response.use((response) => {
    return response;
}, (error) => {
    console.log("Error in response interceptor", error);
    if (error.response?.status === 401) {
        // Handle 401 Unauthorized
        localStorage.removeItem("token");
        window.location.href = "/auth/login"; // Redirect to login page
    }
    return Promise.reject(error); // Propagate other errors
});

export { publicAxios, authAxios };
