import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Hanya ID card 3D yang lewat Vite; CSS/JS lain tetap statis di public/.
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/idcard.js'],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
