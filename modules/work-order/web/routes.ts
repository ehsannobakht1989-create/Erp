import { lazy } from 'react';

export const routes = [
  {
    path: '/work-order',
    permission: 'work-order.record.view',
    permissions: ['work-order.record.view', 'work-order.record.create', 'work-order.record.approve'],
    component: lazy(() => import('./pages/WorkOrderPage')),
  },
  {
    path: '/work-order/:id',
    permission: 'work-order.record.view',
    component: lazy(() => import('./pages/WorkOrderDetailPage')),
  },
  {
    path: '/work-order/manage',
    permission: 'work-order.record.approve',
    component: lazy(() => import('./pages/WorkOrderManagePage')),
  },
];
