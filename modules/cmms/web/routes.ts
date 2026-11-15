import { lazy } from 'react';

export const routes = [
  {
    path: '/cmms',
    permission: 'cmms.record.view',
    permissions: ['cmms.record.view', 'cmms.record.create', 'cmms.record.approve'],
    component: lazy(() => import('./pages/CmmsPage')),
  },
  {
    path: '/cmms/:id',
    permission: 'cmms.record.view',
    component: lazy(() => import('./pages/CmmsDetailPage')),
  },
  {
    path: '/cmms/manage',
    permission: 'cmms.record.approve',
    component: lazy(() => import('./pages/CmmsManagePage')),
  },
];
