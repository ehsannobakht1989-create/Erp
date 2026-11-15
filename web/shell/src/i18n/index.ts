const moduleLangs = import.meta.glob('../../../../modules/*/web/i18n/fa.json', { eager: true });

const shellStrings: Record<string, string> = {
  'shell.dashboard': 'داشبورد',
  'shell.search': 'جستجوی سریع (Ctrl+K)',
  'shell.offline': 'ارتباط با سرور برقرار نیست',
  'shell.forbidden': 'دسترسی غیرمجاز (403)',
  'shell.not_found': 'صفحه یافت نشد (404)',
};

const dictionaries: Record<string, Record<string, string>> = { shell: shellStrings };

for (const [path, mod] of Object.entries(moduleLangs)) {
  const m = path.match(/\/modules\/([^/]+)\/web\/i18n\/fa\.json$/);
  if (m) {
    dictionaries[m[1]] = (mod as { default?: Record<string, string> }).default ?? (mod as Record<string, string>);
  }
}

export function t(key: string, fallback?: string): string {
  const [ns, ...rest] = key.split('.');
  const subKey = rest.join('.');
  if (dictionaries[ns]?.[subKey]) {
    return dictionaries[ns][subKey];
  }
  return dictionaries.shell[key] ?? fallback ?? key;
}
