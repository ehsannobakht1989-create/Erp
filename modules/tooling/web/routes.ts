import { lazy } from 'react';

export const routes = [
  {
    path: '/tooling',
    permission: 'tooling.record.view',
    permissions: ['tooling.record.view', 'tooling.record.create', 'tooling.record.approve'],
    component: lazy(() => import('./pages/ToolingPage')),
  },
  {
    path: '/tooling/:id',
    permission: 'tooling.record.view',
    component: lazy(() => import('./pages/ToolingDetailPage')),
  },
  {
    path: '/tooling/manage',
    permission: 'tooling.record.approve',
    component: lazy(() => import('./pages/ToolingManagePage')),
  },
];
