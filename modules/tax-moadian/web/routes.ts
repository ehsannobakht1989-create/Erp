import { lazy } from 'react';

export const routes = [
  {
    path: '/tax-moadian',
    permission: 'tax-moadian.record.view',
    permissions: ['tax-moadian.record.view', 'tax-moadian.record.create', 'tax-moadian.record.approve'],
    component: lazy(() => import('./pages/TaxMoadianPage')),
  },
  {
    path: '/tax-moadian/:id',
    permission: 'tax-moadian.record.view',
    component: lazy(() => import('./pages/TaxMoadianDetailPage')),
  },
  {
    path: '/tax-moadian/manage',
    permission: 'tax-moadian.record.approve',
    component: lazy(() => import('./pages/TaxMoadianManagePage')),
  },
];
