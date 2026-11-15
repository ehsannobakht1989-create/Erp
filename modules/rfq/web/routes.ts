import { lazy } from 'react';

export const routes = [
  {
    path: '/rfq',
    permission: 'rfq.record.view',
    permissions: ['rfq.record.view', 'rfq.record.create', 'rfq.record.approve'],
    component: lazy(() => import('./pages/RfqPage')),
  },
  {
    path: '/rfq/:id',
    permission: 'rfq.record.view',
    component: lazy(() => import('./pages/RfqDetailPage')),
  },
  {
    path: '/rfq/manage',
    permission: 'rfq.record.approve',
    component: lazy(() => import('./pages/RfqManagePage')),
  },
];
