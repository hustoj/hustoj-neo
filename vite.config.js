import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/sass/app.scss',
                'resources/assets/front/app.js',
                'resources/assets/sass/admin.scss',
                'resources/assets/js/app.js',
            ],
            refresh: true,
        }),
        vue(),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                loadPaths: ['node_modules'],
            },
        },
    },
});
