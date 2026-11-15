import { lazy } from 'react';

export const routes = [
  {
    path: '/energy-utilities',
    permission: 'energy-utilities.record.view',
    permissions: ['energy-utilities.record.view', 'energy-utilities.record.create', 'energy-utilities.record.approve'],
    component: lazy(() => import('./pages/EnergyUtilitiesPage')),
  },
  {
    path: '/energy-utilities/:id',
    permission: 'energy-utilities.record.view',
    component: lazy(() => import('./pages/EnergyUtilitiesDetailPage')),
  },
  {
    path: '/energy-utilities/manage',
    permission: 'energy-utilities.record.approve',
    component: lazy(() => import('./pages/EnergyUtilitiesManagePage')),
  },
];
