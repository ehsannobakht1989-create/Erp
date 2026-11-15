import { lazy } from 'react';

export const routes = [
  {
    path: '/supplier-evaluation',
    permission: 'supplier-evaluation.record.view',
    permissions: ['supplier-evaluation.record.view', 'supplier-evaluation.record.create', 'supplier-evaluation.record.approve'],
    component: lazy(() => import('./pages/SupplierEvaluationPage')),
  },
  {
    path: '/supplier-evaluation/:id',
    permission: 'supplier-evaluation.record.view',
    component: lazy(() => import('./pages/SupplierEvaluationDetailPage')),
  },
  {
    path: '/supplier-evaluation/manage',
    permission: 'supplier-evaluation.record.approve',
    component: lazy(() => import('./pages/SupplierEvaluationManagePage')),
  },
];
