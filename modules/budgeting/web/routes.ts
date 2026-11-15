import { lazy } from 'react';

export const routes = [
  {
    path: '/budgeting',
    permission: 'budgeting.record.view',
    permissions: ['budgeting.record.view', 'budgeting.record.create', 'budgeting.record.approve'],
    component: lazy(() => import('./pages/BudgetingPage')),
  },
  {
    path: '/budgeting/:id',
    permission: 'budgeting.record.view',
    component: lazy(() => import('./pages/BudgetingDetailPage')),
  },
  {
    path: '/budgeting/manage',
    permission: 'budgeting.record.approve',
    component: lazy(() => import('./pages/BudgetingManagePage')),
  },
];
