import { lazy } from 'react';

export const routes = [
  {
    path: '/purchase-request',
    permission: 'purchase-request.record.view',
    permissions: ['purchase-request.record.view', 'purchase-request.record.create', 'purchase-request.record.approve'],
    component: lazy(() => import('./pages/PurchaseRequestPage')),
  },
  {
    path: '/purchase-request/:id',
    permission: 'purchase-request.record.view',
    component: lazy(() => import('./pages/PurchaseRequestDetailPage')),
  },
  {
    path: '/purchase-request/manage',
    permission: 'purchase-request.record.approve',
    component: lazy(() => import('./pages/PurchaseRequestManagePage')),
  },
];
