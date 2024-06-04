import { fileURLToPath, URL } from "node:url";

import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

// Finn mer her https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", import.meta.url)),
    },
  },
  build: {
    rollupOptions: {
      output: {
        dir: './dist/assets/', // Output directory for built files
        entryFileNames: 'build.js', // Main JavaScript file name
        // Combine all CSS into a single file named 'styles.css'
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith(".css")) {
            return "build.css"; // All CSS goes into 'styles.css'
          }
          return assetInfo.name; // Other assets keep their original names
        },
        chunkFileNames: "chunk.js",
        manualChunks: undefined,
      },
    },
  },
});