import { lazy } from 'react';

export const routes = [
  {
    path: '/mes',
    permission: 'mes.record.view',
    permissions: ['mes.record.view', 'mes.record.create', 'mes.record.approve'],
    component: lazy(() => import('./pages/MesPage')),
  },
  {
    path: '/mes/:id',
    permission: 'mes.record.view',
    component: lazy(() => import('./pages/MesDetailPage')),
  },
  {
    path: '/mes/manage',
    permission: 'mes.record.approve',
    component: lazy(() => import('./pages/MesManagePage')),
  },
];
