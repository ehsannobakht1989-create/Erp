import { lazy } from 'react';

export const routes = [
  {
    path: '/eco-plm',
    permission: 'eco-plm.record.view',
    permissions: ['eco-plm.record.view', 'eco-plm.record.create', 'eco-plm.record.approve'],
    component: lazy(() => import('./pages/EcoPlmPage')),
  },
  {
    path: '/eco-plm/:id',
    permission: 'eco-plm.record.view',
    component: lazy(() => import('./pages/EcoPlmDetailPage')),
  },
  {
    path: '/eco-plm/manage',
    permission: 'eco-plm.record.approve',
    component: lazy(() => import('./pages/EcoPlmManagePage')),
  },
];
