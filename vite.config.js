import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '127.0.0.1', // Forzar IPv4
        port: 5174, // Cambia al puerto que prefieras
        strictPort: false, // Permite usar un puerto dinámico si 5173 está ocupado
    },
});
