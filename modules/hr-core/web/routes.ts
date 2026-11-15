import { lazy } from 'react';

export const routes = [
  {
    path: '/hr-core',
    permission: 'hr-core.employee.view',
    permissions: ['hr-core.employee.view', 'hr-core.employee.create', 'hr-core.employee.update', 'hr-core.employee.terminate', 'hr-core.sensitive.view'],
    component: lazy(() => import('./pages/HrCorePage')),
  },
  {
    path: '/hr-core/:id',
    permission: 'hr-core.employee.view',
    component: lazy(() => import('./pages/HrCoreDetailPage')),
  },
  {
    path: '/hr-core/manage',
    permission: 'hr-core.sensitive.view',
    component: lazy(() => import('./pages/HrCoreManagePage')),
  },
];
