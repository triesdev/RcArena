import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/script.js'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    build: {
        outDir: 'public',
        assetsDir: '',
        rollupOptions: {
            output: {
                entryFileNames: () => 'js/app.js',
                chunkFileNames: 'js/app.js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.endsWith('.css')) {
                        return 'css/app.css';
                    }
                    return 'assets/[name][extname]';
                },
            },
        },
        manifest: true,
        emptyOutDir: false,
    },
});
