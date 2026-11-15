import { defineConfig, type Plugin } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'node:path';

function erpProfileFilter(): Plugin {
  const raw = process.env.ERP_MODULES || '';
  const allowed = raw
    .split(',')
    .map((s) => s.trim())
    .filter(Boolean);

  return {
    name: 'erp-profile-filter',
    resolveId(source) {
      if (allowed.length > 0 && source.includes('/modules/')) {
        const match = source.match(/\/modules\/([^/]+)\//);
        if (match && !allowed.includes(match[1])) {
          return '\0erp-disabled-module';
        }
      }
      return null;
    },
    load(id) {
      if (id === '\0erp-disabled-module') {
        return 'export default [];';
      }
      return null;
    },
  };
}

export default defineConfig({
  plugins: [react(), erpProfileFilter()],
  resolve: {
    alias: {
      '@erp/ui': path.resolve(__dirname, 'src/ui/index.tsx'),
      '@erp/i18n': path.resolve(__dirname, 'src/i18n/index.ts'),
      '@erp/hooks': path.resolve(__dirname, 'src/hooks/index.ts'),
    },
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    proxy: {
      '/api': 'http://127.0.0.1:8080',
    },
  },
});
