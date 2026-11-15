import { lazy } from 'react';

export const routes = [
  {
    path: '/accounting-gl',
    permission: 'accounting-gl.account.manage',
    permissions: ['accounting-gl.account.manage', 'accounting-gl.journal.view', 'accounting-gl.journal.create', 'accounting-gl.journal.approve', 'accounting-gl.report.view'],
    component: lazy(() => import('./pages/AccountingGlPage')),
  },
  {
    path: '/accounting-gl/:id',
    permission: 'accounting-gl.account.manage',
    component: lazy(() => import('./pages/AccountingGlDetailPage')),
  },
  {
    path: '/accounting-gl/manage',
    permission: 'accounting-gl.report.view',
    component: lazy(() => import('./pages/AccountingGlManagePage')),
  },
];
