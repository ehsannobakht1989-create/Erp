import { lazy } from 'react';

export const routes = [
  {
    path: '/warranty-after-sales',
    permission: 'warranty-after-sales.record.view',
    permissions: ['warranty-after-sales.record.view', 'warranty-after-sales.record.create', 'warranty-after-sales.record.approve'],
    component: lazy(() => import('./pages/WarrantyAfterSalesPage')),
  },
  {
    path: '/warranty-after-sales/:id',
    permission: 'warranty-after-sales.record.view',
    component: lazy(() => import('./pages/WarrantyAfterSalesDetailPage')),
  },
  {
    path: '/warranty-after-sales/manage',
    permission: 'warranty-after-sales.record.approve',
    component: lazy(() => import('./pages/WarrantyAfterSalesManagePage')),
  },
];
