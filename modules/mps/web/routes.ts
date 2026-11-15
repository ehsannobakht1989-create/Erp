import { lazy } from 'react';

export const routes = [
  {
    path: '/mps',
    permission: 'mps.record.view',
    permissions: ['mps.record.view', 'mps.record.create', 'mps.record.approve'],
    component: lazy(() => import('./pages/MpsPage')),
  },
  {
    path: '/mps/:id',
    permission: 'mps.record.view',
    component: lazy(() => import('./pages/MpsDetailPage')),
  },
  {
    path: '/mps/manage',
    permission: 'mps.record.approve',
    component: lazy(() => import('./pages/MpsManagePage')),
  },
];
