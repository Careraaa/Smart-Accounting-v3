import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/scss/app.scss", // main SCSS entry
                "resources/js/app.js", // main JS entry
            ],
            refresh: true,
        }),
    ],
    server: {
        host: "localhost",
        hmr: {
            host: "localhost",
        },
    },
    resolve: {
        alias: {},
    },
});
