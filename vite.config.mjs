import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import path from "path";

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                "resources/js/app.js",
                "resources/js/ajaxSearch.js",
                "resources/css/app.css",
                "resources/sass/app.scss",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./resources/js"),
            "@api": path.resolve(__dirname, "./resources/js/api"),
            "~": path.resolve(__dirname, "./resources"),
            "@modules": path.resolve(__dirname, "./resources/js/modules"),
        },
    },
    build: {
        emptyOutDir: true,
        rollupOptions: {
            output: {
                entryFileNames: `js/[name]-[hash].js`,
                chunkFileNames: `js/[name]-[hash].js`,
                assetFileNames: (assetInfo) => {
                    const extType = assetInfo.name.split(".").at(1);
                    if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
                        return `images/[name]-[hash][extname]`;
                    }
                    if (/css/i.test(extType)) {
                        return `css/[name]-[hash][extname]`;
                    }
                    if (/woff|woff2|eot|ttf|otf/i.test(extType)) {
                        return `fonts/[name]-[hash][extname]`;
                    }
                    return `assets/[name]-[hash][extname]`;
                },
            },
        },
        outDir: "public/build",
        assetsDir: "",
        manifest: "manifest.json",
    },
});
