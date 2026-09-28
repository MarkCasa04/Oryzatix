import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import { ensureCsrfCookie } from './api/client';
import App from './App';

const root = document.getElementById('root');
if (root) {
    ensureCsrfCookie().finally(() => {
        createRoot(root).render(
            <React.StrictMode>
                <App />
            </React.StrictMode>
        );
    });
}
