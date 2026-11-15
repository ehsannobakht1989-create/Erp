import { lazy } from 'react';

export const routes = [
  {
    path: '/scrap-byproduct',
    permission: 'scrap-byproduct.record.view',
    permissions: ['scrap-byproduct.record.view', 'scrap-byproduct.record.create', 'scrap-byproduct.record.approve'],
    component: lazy(() => import('./pages/ScrapByproductPage')),
  },
  {
    path: '/scrap-byproduct/:id',
    permission: 'scrap-byproduct.record.view',
    component: lazy(() => import('./pages/ScrapByproductDetailPage')),
  },
  {
    path: '/scrap-byproduct/manage',
    permission: 'scrap-byproduct.record.approve',
    component: lazy(() => import('./pages/ScrapByproductManagePage')),
  },
];
