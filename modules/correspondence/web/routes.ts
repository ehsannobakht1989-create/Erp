import { lazy } from 'react';

export const routes = [
  {
    path: '/correspondence',
    permission: 'correspondence.record.view',
    permissions: ['correspondence.record.view', 'correspondence.record.create', 'correspondence.record.approve'],
    component: lazy(() => import('./pages/CorrespondencePage')),
  },
  {
    path: '/correspondence/:id',
    permission: 'correspondence.record.view',
    component: lazy(() => import('./pages/CorrespondenceDetailPage')),
  },
  {
    path: '/correspondence/manage',
    permission: 'correspondence.record.approve',
    component: lazy(() => import('./pages/CorrespondenceManagePage')),
  },
];
