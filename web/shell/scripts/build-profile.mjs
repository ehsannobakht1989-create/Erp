import fs from 'node:fs';
import path from 'node:path';
import { execSync } from 'node:child_process';

const args = process.argv.slice(2);
const idx = args.indexOf('--profile');
const profilePath = idx !== -1 ? args[idx + 1] : '';

let modules = [];
if (profilePath && fs.existsSync(profilePath)) {
  const raw = fs.readFileSync(profilePath, 'utf8');
  const match = raw.match(/modules:\s*\[([^\]]*)\]/);
  if (match) {
    modules = match[1].split(',').map((s) => s.trim().replace(/['"]/g, '')).filter(Boolean);
  }
}

const env = { ...process.env, ERP_MODULES: modules.join(',') };
execSync('npm run build', { stdio: 'inherit', env, cwd: path.resolve(import.meta.dirname, '..') });
