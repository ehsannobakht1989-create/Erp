import { lazy } from 'react';

export const routes = [
  {
    path: '/demand-forecast',
    permission: 'demand-forecast.record.view',
    permissions: ['demand-forecast.record.view', 'demand-forecast.record.create', 'demand-forecast.record.approve'],
    component: lazy(() => import('./pages/DemandForecastPage')),
  },
  {
    path: '/demand-forecast/:id',
    permission: 'demand-forecast.record.view',
    component: lazy(() => import('./pages/DemandForecastDetailPage')),
  },
  {
    path: '/demand-forecast/manage',
    permission: 'demand-forecast.record.approve',
    component: lazy(() => import('./pages/DemandForecastManagePage')),
  },
];
