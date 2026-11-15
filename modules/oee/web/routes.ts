import { lazy } from 'react';

export const routes = [
  {
    path: '/oee',
    permission: 'oee.record.view',
    permissions: ['oee.record.view', 'oee.record.create', 'oee.record.approve'],
    component: lazy(() => import('./pages/OeePage')),
  },
  {
    path: '/oee/:id',
    permission: 'oee.record.view',
    component: lazy(() => import('./pages/OeeDetailPage')),
  },
  {
    path: '/oee/manage',
    permission: 'oee.record.approve',
    component: lazy(() => import('./pages/OeeManagePage')),
  },
];
