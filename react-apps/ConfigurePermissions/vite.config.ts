import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// Builds directly into the served webroot as a committed, non-hashed bundle so that
// deployment stays "copy files to server" -- no Node/build step needed in production.
// Rebuild locally with `npm run build` after changing src/, then commit the dist/ output.
export default defineConfig({
  plugins: [react()],
  build: {
    outDir: '../../webpages/javascript/ConfigurePermissions/dist',
    emptyOutDir: true,
    rollupOptions: {
      input: 'src/main.tsx',
      output: {
        entryFileNames: 'bundle.js',
        chunkFileNames: 'bundle-[name].js',
        assetFileNames: (assetInfo) =>
          assetInfo.name && assetInfo.name.endsWith('.css') ? 'bundle.css' : 'assets/[name][extname]',
      },
    },
  },
});
