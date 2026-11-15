import { lazy } from 'react';

export const routes = [
  {
    path: '/inventory',
    permission: 'inventory.warehouse.manage',
    permissions: ['inventory.warehouse.manage', 'inventory.receipt.view', 'inventory.receipt.create', 'inventory.receipt.post', 'inventory.issue.post', 'inventory.balance.view'],
    component: lazy(() => import('./pages/InventoryPage')),
  },
  {
    path: '/inventory/:id',
    permission: 'inventory.warehouse.manage',
    component: lazy(() => import('./pages/InventoryDetailPage')),
  },
  {
    path: '/inventory/manage',
    permission: 'inventory.balance.view',
    component: lazy(() => import('./pages/InventoryManagePage')),
  },
];
