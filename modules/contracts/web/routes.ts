import { lazy } from 'react';

export const routes = [
  {
    path: '/contracts',
    permission: 'contracts.record.view',
    permissions: ['contracts.record.view', 'contracts.record.create', 'contracts.record.approve'],
    component: lazy(() => import('./pages/ContractsPage')),
  },
  {
    path: '/contracts/:id',
    permission: 'contracts.record.view',
    component: lazy(() => import('./pages/ContractsDetailPage')),
  },
  {
    path: '/contracts/manage',
    permission: 'contracts.record.approve',
    component: lazy(() => import('./pages/ContractsManagePage')),
  },
];
