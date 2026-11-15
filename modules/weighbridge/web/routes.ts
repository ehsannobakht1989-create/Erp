import { lazy } from 'react';

export const routes = [
  {
    path: '/weighbridge',
    permission: 'weighbridge.record.view',
    permissions: ['weighbridge.record.view', 'weighbridge.record.create', 'weighbridge.record.approve'],
    component: lazy(() => import('./pages/WeighbridgePage')),
  },
  {
    path: '/weighbridge/:id',
    permission: 'weighbridge.record.view',
    component: lazy(() => import('./pages/WeighbridgeDetailPage')),
  },
  {
    path: '/weighbridge/manage',
    permission: 'weighbridge.record.approve',
    component: lazy(() => import('./pages/WeighbridgeManagePage')),
  },
];
