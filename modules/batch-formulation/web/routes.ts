import { lazy } from 'react';

export const routes = [
  {
    path: '/batch-formulation',
    permission: 'batch-formulation.record.view',
    permissions: ['batch-formulation.record.view', 'batch-formulation.record.create', 'batch-formulation.record.approve'],
    component: lazy(() => import('./pages/BatchFormulationPage')),
  },
  {
    path: '/batch-formulation/:id',
    permission: 'batch-formulation.record.view',
    component: lazy(() => import('./pages/BatchFormulationDetailPage')),
  },
  {
    path: '/batch-formulation/manage',
    permission: 'batch-formulation.record.approve',
    component: lazy(() => import('./pages/BatchFormulationManagePage')),
  },
];
