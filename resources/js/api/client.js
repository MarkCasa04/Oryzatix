import axios from 'axios';

function getApiBase() {
    const loc = window.location;
    const path = loc.pathname;
    const publicIndex = path.indexOf('/public');
    if (publicIndex !== -1) {
        return loc.origin + path.substring(0, publicIndex + 7) + '/api/v1';
    }
    if (path.toLowerCase().startsWith('/oryzatix')) {
        return loc.origin + '/Oryzatix/public/api/v1';
    }
    if (path.startsWith('/Gregorio_Alaissa')) {
        return loc.origin + '/Gregorio_Alaissa/public/api/v1';
    }
    return '/api/v1';
}

function getCsrfUrl() {
    const loc = window.location;
    const path = loc.pathname;
    const publicIndex = path.indexOf('/public');
    if (publicIndex !== -1) {
        return loc.origin + path.substring(0, publicIndex + 7) + '/sanctum/csrf-cookie';
    }
    if (path.toLowerCase().startsWith('/oryzatix')) {
        return loc.origin + '/Oryzatix/public/sanctum/csrf-cookie';
    }
    if (path.startsWith('/Gregorio_Alaissa')) {
        return loc.origin + '/Gregorio_Alaissa/public/sanctum/csrf-cookie';
    }
    return '/sanctum/csrf-cookie';
}

const api = axios.create({
    baseURL: getApiBase(),
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
});

let csrfReady = false;

/** Required for Laravel session auth (register/login) from the React SPA */
export async function ensureCsrfCookie(force = false) {
    if (csrfReady && !force) return;
    try {
        await axios.get(getCsrfUrl(), { withCredentials: true });
        csrfReady = true;
    } catch (e) {
        console.warn('CSRF cookie error:', e);
    }
}

api.interceptors.request.use((config) => {
    const token = typeof window !== 'undefined' ? sessionStorage.getItem('oryzatix_token') : null;
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 419) {
            csrfReady = false;
        }
        return Promise.reject(error);
    }
);

export function setAuthToken(token) {
    if (typeof window !== 'undefined' && window.localStorage) {
        localStorage.removeItem('oryzatix_token');
        localStorage.removeItem('oryzatix_auth_token');
    }
    if (typeof window !== 'undefined' && window.sessionStorage) {
        if (token) {
            sessionStorage.setItem('oryzatix_token', token);
        } else {
            sessionStorage.removeItem('oryzatix_token');
        }
    }
}

export function getAuthToken() {
    if (typeof window !== 'undefined' && window.localStorage) {
        localStorage.removeItem('oryzatix_token');
        localStorage.removeItem('oryzatix_auth_token');
    }
    return typeof window !== 'undefined' ? sessionStorage.getItem('oryzatix_token') : null;
}

export default api;
