import React, { Suspense, useState } from 'react';
import { BrowserRouter, Link, Route, Routes } from 'react-router-dom';
import { Card } from '@erp/ui';

const routeModules = import.meta.glob('../../../modules/*/web/routes.ts', { eager: true });

interface ModuleRoute {
  path: string;
  component: React.ComponentType;
  permission?: string;
}

const discoveredRoutes: ModuleRoute[] = [];
for (const mod of Object.values(routeModules)) {
  const list = (mod as { default?: ModuleRoute[] }).default;
  if (Array.isArray(list)) {
    discoveredRoutes.push(...list);
  }
}

export default function App() {
  const [dark, setDark] = useState(false);

  return (
    <BrowserRouter>
      <div className={dark ? 'dark' : ''}>
        <div dir="rtl" className="min-h-screen flex bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100">
          <aside className="w-64 border-l dark:border-slate-800 bg-white dark:bg-slate-900 p-4 space-y-4">
            <div className="font-bold text-lg">ERP تولیدی</div>
            <nav className="space-y-1 text-sm">
              <Link to="/" className="block px-2 py-1.5 rounded hover:bg-slate-100 dark:hover:bg-slate-800">
                داشبورد
              </Link>
              {discoveredRoutes.map((r) => (
                <Link key={r.path} to={r.path} className="block px-2 py-1.5 rounded hover:bg-slate-100 dark:hover:bg-slate-800">
                  {r.path}
                </Link>
              ))}
            </nav>
          </aside>

          <div className="flex-1 flex flex-col">
            <header className="h-14 border-b dark:border-slate-800 bg-white dark:bg-slate-900 px-4 flex items-center justify-between">
              <div className="text-sm text-slate-500">شرکت نمونه — سال مالی ۱۴۰۳</div>
              <button onClick={() => setDark(!dark)} className="text-xs border rounded px-2 py-1">
                {dark ? 'حالت روشن' : 'حالت تاریک'}
              </button>
            </header>

            <main className="flex-1 p-4">
              <Suspense fallback={<div>در حال بارگذاری...</div>}>
                <Routes>
                  <Route
                    path="/"
                    element={
                      <Card title="داشبورد">
                        <p className="text-sm">تعداد مسیرهای کشف‌شده ماژول‌ها: {discoveredRoutes.length}</p>
                      </Card>
                    }
                  />
                  {discoveredRoutes.map((r) => {
                    const Comp = r.component;
                    return <Route key={r.path} path={r.path} element={<Comp />} />;
                  })}
                  <Route path="*" element={<Card title="404">صفحه یافت نشد</Card>} />
                </Routes>
              </Suspense>
            </main>
          </div>
        </div>
      </div>
    </BrowserRouter>
  );
}
