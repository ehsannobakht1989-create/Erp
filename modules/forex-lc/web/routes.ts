import { lazy } from 'react';

export const routes = [
  {
    path: '/forex-lc',
    permission: 'forex-lc.record.view',
    permissions: ['forex-lc.record.view', 'forex-lc.record.create', 'forex-lc.record.approve'],
    component: lazy(() => import('./pages/ForexLcPage')),
  },
  {
    path: '/forex-lc/:id',
    permission: 'forex-lc.record.view',
    component: lazy(() => import('./pages/ForexLcDetailPage')),
  },
  {
    path: '/forex-lc/manage',
    permission: 'forex-lc.record.approve',
    component: lazy(() => import('./pages/ForexLcManagePage')),
  },
];
