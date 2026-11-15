import { lazy } from 'react';

export const routes = [
  {
    path: '/routing',
    permission: 'routing.record.view',
    permissions: ['routing.record.view', 'routing.record.create', 'routing.record.approve'],
    component: lazy(() => import('./pages/RoutingPage')),
  },
  {
    path: '/routing/:id',
    permission: 'routing.record.view',
    component: lazy(() => import('./pages/RoutingDetailPage')),
  },
  {
    path: '/routing/manage',
    permission: 'routing.record.approve',
    component: lazy(() => import('./pages/RoutingManagePage')),
  },
];
