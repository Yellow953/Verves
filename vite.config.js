import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        react({
            jsxRuntime: 'automatic',
        }),
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/frontend/main.jsx',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js/frontend',
        },
    },
    optimizeDeps: {
        include: ['react', 'react-dom', 'react-i18next'],
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
        },
    },
});
