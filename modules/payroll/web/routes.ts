import { lazy } from 'react';

export const routes = [
  {
    path: '/payroll',
    permission: 'payroll.record.view',
    permissions: ['payroll.record.view', 'payroll.record.create', 'payroll.record.approve'],
    component: lazy(() => import('./pages/PayrollPage')),
  },
  {
    path: '/payroll/:id',
    permission: 'payroll.record.view',
    component: lazy(() => import('./pages/PayrollDetailPage')),
  },
  {
    path: '/payroll/manage',
    permission: 'payroll.record.approve',
    component: lazy(() => import('./pages/PayrollManagePage')),
  },
];
