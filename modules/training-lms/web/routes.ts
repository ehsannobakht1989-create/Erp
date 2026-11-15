import { lazy } from 'react';

export const routes = [
  {
    path: '/training-lms',
    permission: 'training-lms.record.view',
    permissions: ['training-lms.record.view', 'training-lms.record.create', 'training-lms.record.approve'],
    component: lazy(() => import('./pages/TrainingLmsPage')),
  },
  {
    path: '/training-lms/:id',
    permission: 'training-lms.record.view',
    component: lazy(() => import('./pages/TrainingLmsDetailPage')),
  },
  {
    path: '/training-lms/manage',
    permission: 'training-lms.record.approve',
    component: lazy(() => import('./pages/TrainingLmsManagePage')),
  },
];
