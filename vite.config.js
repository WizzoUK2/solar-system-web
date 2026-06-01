import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Self-hosted at build time (no runtime Google Fonts) — privacy + perf.
            // Inter for body, Newsreader serif for headings (BRIEF.md §6). English
            // only at launch, so latin subset only; preload just the two primary
            // weights to stay inside the performance budget.
            fonts: [
                bunny('Inter', {
                    weights: [400, 500, 600, 700],
                    subsets: ['latin'],
                    display: 'swap',
                    preload: false,
                }),
                bunny('Newsreader', {
                    weights: [400, 500, 600],
                    subsets: ['latin'],
                    display: 'swap',
                    preload: false,
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
