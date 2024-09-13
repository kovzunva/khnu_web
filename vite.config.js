import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
 
export default defineConfig({
    plugins: [
        laravel([
            'resources/css/my_style.css',
            'resources/css/summernote-lite.min.css',
            'resources/js/jquery.min.js',
            'resources/js/most_used.js',
            'resources/js/client.js',
            'resources/js/content_maker.js',
            'resources/js/specified.js',
            'resources/js/edition.js',
            'resources/js/summernote-lite.min.js',
            'resources/js/charts.js',
        ]),
    ],
    optimizeDeps: {
      include: ['jquery'],
    },
});