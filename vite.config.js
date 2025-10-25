import { resolve } from 'path';
import { defineConfig } from 'vite';
import { globSync } from 'glob';
import tailwindcss from '@tailwindcss/vite';

// Mengambil semua file HTML dari folder src/page
const pageFiles = globSync('src/page/*.html');

// Membuat objek input untuk Rollup
const input = pageFiles.reduce((acc, file) => {
  // Mendapatkan nama file tanpa ekstensi (e.g., 'about_us')
  const name = file.split('/').pop().replace('.html', '');
  acc[name] = resolve(__dirname, file);
  return acc;
}, {});

export default defineConfig({
  build: {
    rollupOptions: {
      input: {
        // Jangan lupa daftarkan juga index.html utama di root
        main: resolve(__dirname, 'index.html'),
        // Sebarkan (spread) semua file HTML dari folder page
        ...input,
      },
    },
  },
  plugins: [tailwindcss()]
});