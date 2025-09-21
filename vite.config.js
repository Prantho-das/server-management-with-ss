import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css',
                'resources/css/themes/red.css',
                'resources/css/themes/green.css',
                'resources/css/themes/blue.css',
            ],
            refresh: true,
        }),
        tailwindcss({
            config: './resources/css/filament/admin/tailwind.config.js',
        }),
    ],
    server: {
        cors: true,
    },
});