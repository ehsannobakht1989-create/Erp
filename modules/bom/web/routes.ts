import { lazy } from 'react';

export const routes = [
  {
    path: '/bom',
    permission: 'bom.bom.view',
    permissions: ['bom.bom.view', 'bom.bom.create', 'bom.bom.update', 'bom.bom.approve', 'bom.bom.delete'],
    component: lazy(() => import('./pages/BomPage')),
  },
  {
    path: '/bom/:id',
    permission: 'bom.bom.view',
    component: lazy(() => import('./pages/BomDetailPage')),
  },
  {
    path: '/bom/manage',
    permission: 'bom.bom.delete',
    component: lazy(() => import('./pages/BomManagePage')),
  },
];
