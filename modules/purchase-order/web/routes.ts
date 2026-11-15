import { lazy } from 'react';

export const routes = [
  {
    path: '/purchase-order',
    permission: 'purchase-order.record.view',
    permissions: ['purchase-order.record.view', 'purchase-order.record.create', 'purchase-order.record.approve'],
    component: lazy(() => import('./pages/PurchaseOrderPage')),
  },
  {
    path: '/purchase-order/:id',
    permission: 'purchase-order.record.view',
    component: lazy(() => import('./pages/PurchaseOrderDetailPage')),
  },
  {
    path: '/purchase-order/manage',
    permission: 'purchase-order.record.approve',
    component: lazy(() => import('./pages/PurchaseOrderManagePage')),
  },
];
