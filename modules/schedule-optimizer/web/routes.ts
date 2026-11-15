import { lazy } from 'react';

export const routes = [
  {
    path: '/schedule-optimizer',
    permission: 'schedule-optimizer.record.view',
    permissions: ['schedule-optimizer.record.view', 'schedule-optimizer.record.create', 'schedule-optimizer.record.approve'],
    component: lazy(() => import('./pages/ScheduleOptimizerPage')),
  },
  {
    path: '/schedule-optimizer/:id',
    permission: 'schedule-optimizer.record.view',
    component: lazy(() => import('./pages/ScheduleOptimizerDetailPage')),
  },
  {
    path: '/schedule-optimizer/manage',
    permission: 'schedule-optimizer.record.approve',
    component: lazy(() => import('./pages/ScheduleOptimizerManagePage')),
  },
];
