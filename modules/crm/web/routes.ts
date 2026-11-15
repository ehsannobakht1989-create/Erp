import { lazy } from 'react';

export const routes = [
  {
    path: '/crm',
    permission: 'crm.record.view',
    permissions: ['crm.record.view', 'crm.record.create', 'crm.record.approve'],
    component: lazy(() => import('./pages/CrmPage')),
  },
  {
    path: '/crm/:id',
    permission: 'crm.record.view',
    component: lazy(() => import('./pages/CrmDetailPage')),
  },
  {
    path: '/crm/manage',
    permission: 'crm.record.approve',
    component: lazy(() => import('./pages/CrmManagePage')),
  },
];
