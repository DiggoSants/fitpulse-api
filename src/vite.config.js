import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/style.css', // <--- ADICIONE ESTA LINHA
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
});
