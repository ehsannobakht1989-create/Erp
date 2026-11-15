import { lazy } from 'react';

export const routes = [
  {
    path: '/recruitment',
    permission: 'recruitment.record.view',
    permissions: ['recruitment.record.view', 'recruitment.record.create', 'recruitment.record.approve'],
    component: lazy(() => import('./pages/RecruitmentPage')),
  },
  {
    path: '/recruitment/:id',
    permission: 'recruitment.record.view',
    component: lazy(() => import('./pages/RecruitmentDetailPage')),
  },
  {
    path: '/recruitment/manage',
    permission: 'recruitment.record.approve',
    component: lazy(() => import('./pages/RecruitmentManagePage')),
  },
];
