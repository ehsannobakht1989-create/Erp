import { lazy } from 'react';

export const routes = [
  {
    path: '/contractor-mgmt',
    permission: 'contractor-mgmt.record.view',
    permissions: ['contractor-mgmt.record.view', 'contractor-mgmt.record.create', 'contractor-mgmt.record.approve'],
    component: lazy(() => import('./pages/ContractorMgmtPage')),
  },
  {
    path: '/contractor-mgmt/:id',
    permission: 'contractor-mgmt.record.view',
    component: lazy(() => import('./pages/ContractorMgmtDetailPage')),
  },
  {
    path: '/contractor-mgmt/manage',
    permission: 'contractor-mgmt.record.approve',
    component: lazy(() => import('./pages/ContractorMgmtManagePage')),
  },
];
