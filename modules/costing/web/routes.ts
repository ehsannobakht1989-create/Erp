import { lazy } from 'react';

export const routes = [
  {
    path: '/costing',
    permission: 'costing.record.view',
    permissions: ['costing.record.view', 'costing.record.create', 'costing.record.approve'],
    component: lazy(() => import('./pages/CostingPage')),
  },
  {
    path: '/costing/:id',
    permission: 'costing.record.view',
    component: lazy(() => import('./pages/CostingDetailPage')),
  },
  {
    path: '/costing/manage',
    permission: 'costing.record.approve',
    component: lazy(() => import('./pages/CostingManagePage')),
  },
];
