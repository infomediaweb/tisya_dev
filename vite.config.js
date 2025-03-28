import { createRequire } from 'node:module'
import { defineConfig } from 'vite'
import path from 'path'
import autoprefixer from 'autoprefixer'

const require = createRequire( import.meta.url )

import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import ckeditor5 from '@ckeditor/vite-plugin-ckeditor5'

export default defineConfig({
    // define: {
	// 	'process.env': process.env,
	// },
    resolve: {
        alias: {
            '@assets': path.resolve(__dirname, 'resources/js/assets'),
            '@components': path.resolve(__dirname, 'resources/js/components'),
            '@utils': path.resolve(__dirname, 'resources/js/utils'),
        }
    },
    plugins: [
        vue(),
        ckeditor5({ 
            theme: require.resolve('@ckeditor/ckeditor5-theme-lark') 
        }),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: false,
        }),
    ],
    css: {
        postcss: {
            plugins: [
                autoprefixer({
                    overrideBrowserslist: 'last 4 versions'
                })
            ]
        }
    },
    build: {
        //manifest: true,
    } 
});
