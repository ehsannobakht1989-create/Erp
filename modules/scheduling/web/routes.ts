import { lazy } from 'react';

export const routes = [
  {
    path: '/scheduling',
    permission: 'scheduling.record.view',
    permissions: ['scheduling.record.view', 'scheduling.record.create', 'scheduling.record.approve'],
    component: lazy(() => import('./pages/SchedulingPage')),
  },
  {
    path: '/scheduling/:id',
    permission: 'scheduling.record.view',
    component: lazy(() => import('./pages/SchedulingDetailPage')),
  },
  {
    path: '/scheduling/manage',
    permission: 'scheduling.record.approve',
    component: lazy(() => import('./pages/SchedulingManagePage')),
  },
];
