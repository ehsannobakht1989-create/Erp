import { lazy } from 'react';

export const routes = [
  {
    path: '/hse',
    permission: 'hse.record.view',
    permissions: ['hse.record.view', 'hse.record.create', 'hse.record.approve'],
    component: lazy(() => import('./pages/HsePage')),
  },
  {
    path: '/hse/:id',
    permission: 'hse.record.view',
    component: lazy(() => import('./pages/HseDetailPage')),
  },
  {
    path: '/hse/manage',
    permission: 'hse.record.approve',
    component: lazy(() => import('./pages/HseManagePage')),
  },
];
