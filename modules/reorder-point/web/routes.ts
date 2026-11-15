import { lazy } from 'react';

export const routes = [
  {
    path: '/reorder-point',
    permission: 'reorder-point.record.view',
    permissions: ['reorder-point.record.view', 'reorder-point.record.create', 'reorder-point.record.approve'],
    component: lazy(() => import('./pages/ReorderPointPage')),
  },
  {
    path: '/reorder-point/:id',
    permission: 'reorder-point.record.view',
    component: lazy(() => import('./pages/ReorderPointDetailPage')),
  },
  {
    path: '/reorder-point/manage',
    permission: 'reorder-point.record.approve',
    component: lazy(() => import('./pages/ReorderPointManagePage')),
  },
];
