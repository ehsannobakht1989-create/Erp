import { lazy } from 'react';

export const routes = [
  {
    path: '/item-master',
    permission: 'item-master.item.view',
    permissions: ['item-master.item.view', 'item-master.item.create', 'item-master.item.update', 'item-master.item.delete', 'item-master.item.approve', 'item-master.item.import', 'item-master.item.export', 'item-master.item.merge', 'item-master.category.manage', 'item-master.coding.manage', 'item-master.alternate.manage', 'item-master.ai.use'],
    component: lazy(() => import('./pages/ItemMasterPage')),
  },
  {
    path: '/item-master/:id',
    permission: 'item-master.item.view',
    component: lazy(() => import('./pages/ItemMasterDetailPage')),
  },
  {
    path: '/item-master/manage',
    permission: 'item-master.ai.use',
    component: lazy(() => import('./pages/ItemMasterManagePage')),
  },
];
