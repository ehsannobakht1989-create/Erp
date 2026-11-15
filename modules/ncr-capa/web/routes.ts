import { lazy } from 'react';

export const routes = [
  {
    path: '/ncr-capa',
    permission: 'ncr-capa.record.view',
    permissions: ['ncr-capa.record.view', 'ncr-capa.record.create', 'ncr-capa.record.approve'],
    component: lazy(() => import('./pages/NcrCapaPage')),
  },
  {
    path: '/ncr-capa/:id',
    permission: 'ncr-capa.record.view',
    component: lazy(() => import('./pages/NcrCapaDetailPage')),
  },
  {
    path: '/ncr-capa/manage',
    permission: 'ncr-capa.record.approve',
    component: lazy(() => import('./pages/NcrCapaManagePage')),
  },
];
