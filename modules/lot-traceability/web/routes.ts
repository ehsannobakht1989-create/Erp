import { lazy } from 'react';

export const routes = [
  {
    path: '/lot-traceability',
    permission: 'lot-traceability.record.view',
    permissions: ['lot-traceability.record.view', 'lot-traceability.record.create', 'lot-traceability.record.approve'],
    component: lazy(() => import('./pages/LotTraceabilityPage')),
  },
  {
    path: '/lot-traceability/:id',
    permission: 'lot-traceability.record.view',
    component: lazy(() => import('./pages/LotTraceabilityDetailPage')),
  },
  {
    path: '/lot-traceability/manage',
    permission: 'lot-traceability.record.approve',
    component: lazy(() => import('./pages/LotTraceabilityManagePage')),
  },
];
