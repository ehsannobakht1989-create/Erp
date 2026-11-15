import { lazy } from 'react';

export const routes = [
  {
    path: '/pricing',
    permission: 'pricing.list.view',
    permissions: ['pricing.list.view', 'pricing.list.manage', 'pricing.list.approve', 'pricing.override'],
    component: lazy(() => import('./pages/PricingPage')),
  },
  {
    path: '/pricing/:id',
    permission: 'pricing.list.view',
    component: lazy(() => import('./pages/PricingDetailPage')),
  },
  {
    path: '/pricing/manage',
    permission: 'pricing.override',
    component: lazy(() => import('./pages/PricingManagePage')),
  },
];
