import { lazy } from 'react';

export const routes = [
  {
    path: '/treasury',
    permission: 'treasury.record.view',
    permissions: ['treasury.record.view', 'treasury.record.create', 'treasury.record.approve'],
    component: lazy(() => import('./pages/TreasuryPage')),
  },
  {
    path: '/treasury/:id',
    permission: 'treasury.record.view',
    component: lazy(() => import('./pages/TreasuryDetailPage')),
  },
  {
    path: '/treasury/manage',
    permission: 'treasury.record.approve',
    component: lazy(() => import('./pages/TreasuryManagePage')),
  },
];
