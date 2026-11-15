import { lazy } from 'react';

export const routes = [
  {
    path: '/management-consolidation',
    permission: 'management-consolidation.record.view',
    permissions: ['management-consolidation.record.view', 'management-consolidation.record.create', 'management-consolidation.record.approve'],
    component: lazy(() => import('./pages/ManagementConsolidationPage')),
  },
  {
    path: '/management-consolidation/:id',
    permission: 'management-consolidation.record.view',
    component: lazy(() => import('./pages/ManagementConsolidationDetailPage')),
  },
  {
    path: '/management-consolidation/manage',
    permission: 'management-consolidation.record.approve',
    component: lazy(() => import('./pages/ManagementConsolidationManagePage')),
  },
];
