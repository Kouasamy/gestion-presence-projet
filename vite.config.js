import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/login.css',
                'resources/css/style.css',
                'resources/css/responsive.css',
                'resources/js/app.js',
                'resources/js/responsive-menu.js'
            ],
            refresh: true,
        }),
    ],
});

