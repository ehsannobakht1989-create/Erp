import { lazy } from 'react';

export const routes = [
  {
    path: '/waste-environment',
    permission: 'waste-environment.record.view',
    permissions: ['waste-environment.record.view', 'waste-environment.record.create', 'waste-environment.record.approve'],
    component: lazy(() => import('./pages/WasteEnvironmentPage')),
  },
  {
    path: '/waste-environment/:id',
    permission: 'waste-environment.record.view',
    component: lazy(() => import('./pages/WasteEnvironmentDetailPage')),
  },
  {
    path: '/waste-environment/manage',
    permission: 'waste-environment.record.approve',
    component: lazy(() => import('./pages/WasteEnvironmentManagePage')),
  },
];
