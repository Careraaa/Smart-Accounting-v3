import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                "resources/css/tailwind.css",
                "resources/css/overrides.css",
                "resources/js/app.js",
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
