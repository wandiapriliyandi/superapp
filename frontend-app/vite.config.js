import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

const replaceApiUrlPlugin = () => {
  return {
    name: 'replace-api-url',
    transform(code, id) {
      if (id.includes('/src/') && (id.endsWith('.vue') || id.endsWith('.js'))) {
        const newCode = code.replace(/(['"])http:\/\/127\.0\.0\.1:8080/g, (match, quote) => {
          return `(window.location.port === "5173" ? "http://127.0.0.1:8080" : window.location.origin + "/superapp/public") + ${quote}`;
        });
        return {
          code: newCode,
          map: null
        };
      }
    }
  }
}

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(), replaceApiUrlPlugin()],
  // Dev server: tetap bisa diakses di http://localhost:5173/
  // Build: output ke ../public/app/ agar bisa diakses via http://localhost/superapp/public/app/
  base: '/superapp/public/app/',
  build: {
    outDir: '../public/app',
    emptyOutDir: true,
  }
})
