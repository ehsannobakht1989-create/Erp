import { lazy } from 'react';

export const routes = [
  {
    path: '/mrp',
    permission: 'mrp.record.view',
    permissions: ['mrp.record.view', 'mrp.record.create', 'mrp.record.approve'],
    component: lazy(() => import('./pages/MrpPage')),
  },
  {
    path: '/mrp/:id',
    permission: 'mrp.record.view',
    component: lazy(() => import('./pages/MrpDetailPage')),
  },
  {
    path: '/mrp/manage',
    permission: 'mrp.record.approve',
    component: lazy(() => import('./pages/MrpManagePage')),
  },
];
