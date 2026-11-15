import { lazy } from 'react';

export const routes = [
  {
    path: '/multi-company',
    permission: 'multi-company.company.view',
    permissions: ['multi-company.company.view', 'multi-company.company.manage', 'multi-company.settings.manage', 'multi-company.ai.use'],
    component: lazy(() => import('./pages/MultiCompanyPage')),
  },
  {
    path: '/multi-company/:id',
    permission: 'multi-company.company.view',
    component: lazy(() => import('./pages/MultiCompanyDetailPage')),
  },
  {
    path: '/multi-company/manage',
    permission: 'multi-company.ai.use',
    component: lazy(() => import('./pages/MultiCompanyManagePage')),
  },
];
