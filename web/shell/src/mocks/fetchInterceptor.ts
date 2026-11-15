export function installMockFetch(): void {
  if (import.meta.env.VITE_MOCK !== '1') return;

  const originalFetch = window.fetch.bind(window);
  window.fetch = async (input: RequestInfo | URL, init?: RequestInit): Promise<Response> => {
    const url = typeof input === 'string' ? input : input.toString();
    if (url.startsWith('/api/_health')) {
      return new Response(JSON.stringify({ ok: true, data: { status: 'healthy', mock: true }, error: null, meta: {} }));
    }
    if (url.startsWith('/api/_meta/modules')) {
      return new Response(
        JSON.stringify({
          ok: true,
          data: {
            modules: [],
            menus: [
              { id: 'auth-rbac', title: { fa: 'کاربران و دسترسی‌ها' }, route: '/auth-rbac' },
              { id: 'multi-company', title: { fa: 'شرکت‌ها و تنظیمات' }, route: '/multi-company' },
              { id: 'audit-log', title: { fa: 'گزارش ممیزی' }, route: '/audit-log' },
            ],
            permissions: ['*'],
          },
          error: null,
          meta: {},
        })
      );
    }
    return originalFetch(input, init);
  };
}
