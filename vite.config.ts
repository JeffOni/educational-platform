import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';

export default defineConfig({
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        // En Windows/Laragon, 'localhost' puede resolverse a IPv6 (::1) y provocar
        // que el navegador no pueda conectar al dev server desde 127.0.0.1.
        host: '127.0.0.1',
        port: 5173,
        strictPort: false,
        hmr: {
            host: 'educational-platform.test',
            port: 5173,
            clientPort: 5173,
        },
    },
});
