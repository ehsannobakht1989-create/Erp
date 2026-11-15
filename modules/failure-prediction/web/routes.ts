import { lazy } from 'react';

export const routes = [
  {
    path: '/failure-prediction',
    permission: 'failure-prediction.record.view',
    permissions: ['failure-prediction.record.view', 'failure-prediction.record.create', 'failure-prediction.record.approve'],
    component: lazy(() => import('./pages/FailurePredictionPage')),
  },
  {
    path: '/failure-prediction/:id',
    permission: 'failure-prediction.record.view',
    component: lazy(() => import('./pages/FailurePredictionDetailPage')),
  },
  {
    path: '/failure-prediction/manage',
    permission: 'failure-prediction.record.approve',
    component: lazy(() => import('./pages/FailurePredictionManagePage')),
  },
];
