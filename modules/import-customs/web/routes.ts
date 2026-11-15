import { lazy } from 'react';

export const routes = [
  {
    path: '/import-customs',
    permission: 'import-customs.record.view',
    permissions: ['import-customs.record.view', 'import-customs.record.create', 'import-customs.record.approve'],
    component: lazy(() => import('./pages/ImportCustomsPage')),
  },
  {
    path: '/import-customs/:id',
    permission: 'import-customs.record.view',
    component: lazy(() => import('./pages/ImportCustomsDetailPage')),
  },
  {
    path: '/import-customs/manage',
    permission: 'import-customs.record.approve',
    component: lazy(() => import('./pages/ImportCustomsManagePage')),
  },
];
