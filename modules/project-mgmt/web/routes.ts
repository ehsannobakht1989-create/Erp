import { lazy } from 'react';

export const routes = [
  {
    path: '/project-mgmt',
    permission: 'project-mgmt.record.view',
    permissions: ['project-mgmt.record.view', 'project-mgmt.record.create', 'project-mgmt.record.approve'],
    component: lazy(() => import('./pages/ProjectMgmtPage')),
  },
  {
    path: '/project-mgmt/:id',
    permission: 'project-mgmt.record.view',
    component: lazy(() => import('./pages/ProjectMgmtDetailPage')),
  },
  {
    path: '/project-mgmt/manage',
    permission: 'project-mgmt.record.approve',
    component: lazy(() => import('./pages/ProjectMgmtManagePage')),
  },
];
