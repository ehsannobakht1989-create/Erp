import { lazy } from 'react';

export const routes = [
  {
    path: '/fixed-assets',
    permission: 'fixed-assets.record.view',
    permissions: ['fixed-assets.record.view', 'fixed-assets.record.create', 'fixed-assets.record.approve'],
    component: lazy(() => import('./pages/FixedAssetsPage')),
  },
  {
    path: '/fixed-assets/:id',
    permission: 'fixed-assets.record.view',
    component: lazy(() => import('./pages/FixedAssetsDetailPage')),
  },
  {
    path: '/fixed-assets/manage',
    permission: 'fixed-assets.record.approve',
    component: lazy(() => import('./pages/FixedAssetsManagePage')),
  },
];
