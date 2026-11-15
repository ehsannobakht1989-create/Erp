import { lazy } from 'react';

export const routes = [
  {
    path: '/shipping-distribution',
    permission: 'shipping-distribution.record.view',
    permissions: ['shipping-distribution.record.view', 'shipping-distribution.record.create', 'shipping-distribution.record.approve'],
    component: lazy(() => import('./pages/ShippingDistributionPage')),
  },
  {
    path: '/shipping-distribution/:id',
    permission: 'shipping-distribution.record.view',
    component: lazy(() => import('./pages/ShippingDistributionDetailPage')),
  },
  {
    path: '/shipping-distribution/manage',
    permission: 'shipping-distribution.record.approve',
    component: lazy(() => import('./pages/ShippingDistributionManagePage')),
  },
];
