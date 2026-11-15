import { lazy } from 'react';

export const routes = [
  {
    path: '/lims',
    permission: 'lims.record.view',
    permissions: ['lims.record.view', 'lims.record.create', 'lims.record.approve'],
    component: lazy(() => import('./pages/LimsPage')),
  },
  {
    path: '/lims/:id',
    permission: 'lims.record.view',
    component: lazy(() => import('./pages/LimsDetailPage')),
  },
  {
    path: '/lims/manage',
    permission: 'lims.record.approve',
    component: lazy(() => import('./pages/LimsManagePage')),
  },
];
