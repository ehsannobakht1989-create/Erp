import { lazy } from 'react';

export const routes = [
  {
    path: '/ar-ap',
    permission: 'ar-ap.record.view',
    permissions: ['ar-ap.record.view', 'ar-ap.record.create', 'ar-ap.record.approve'],
    component: lazy(() => import('./pages/ArApPage')),
  },
  {
    path: '/ar-ap/:id',
    permission: 'ar-ap.record.view',
    component: lazy(() => import('./pages/ArApDetailPage')),
  },
  {
    path: '/ar-ap/manage',
    permission: 'ar-ap.record.approve',
    component: lazy(() => import('./pages/ArApManagePage')),
  },
];
