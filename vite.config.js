import {
  defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
const host = 'localhost';
export default defineConfig( {
  server: {
    host,
    hmr: {
      host
    },
    watch: {
      ignored: /node_modules|vendor/,
    },
  },
  plugins: [
    laravel( {
      input: 'resources/js/app.jsx',
      ssr: 'resources/js/ssr.jsx',
      refresh: true,
    }),
    react(),
  ],
});