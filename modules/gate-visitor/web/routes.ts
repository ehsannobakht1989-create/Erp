import { lazy } from 'react';

export const routes = [
  {
    path: '/gate-visitor',
    permission: 'gate-visitor.record.view',
    permissions: ['gate-visitor.record.view', 'gate-visitor.record.create', 'gate-visitor.record.approve'],
    component: lazy(() => import('./pages/GateVisitorPage')),
  },
  {
    path: '/gate-visitor/:id',
    permission: 'gate-visitor.record.view',
    component: lazy(() => import('./pages/GateVisitorDetailPage')),
  },
  {
    path: '/gate-visitor/manage',
    permission: 'gate-visitor.record.approve',
    component: lazy(() => import('./pages/GateVisitorManagePage')),
  },
];
