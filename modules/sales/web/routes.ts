import { lazy } from 'react';

export const routes = [
  {
    path: '/sales',
    permission: 'sales.record.view',
    permissions: ['sales.record.view', 'sales.record.create', 'sales.record.approve'],
    component: lazy(() => import('./pages/SalesPage')),
  },
  {
    path: '/sales/:id',
    permission: 'sales.record.view',
    component: lazy(() => import('./pages/SalesDetailPage')),
  },
  {
    path: '/sales/manage',
    permission: 'sales.record.approve',
    component: lazy(() => import('./pages/SalesManagePage')),
  },
];
