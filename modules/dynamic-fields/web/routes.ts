import { lazy } from 'react';

export const routes = [
  {
    path: '/dynamic-fields',
    permission: 'dynamic-fields.field.view',
    permissions: ['dynamic-fields.field.view', 'dynamic-fields.field.manage', 'dynamic-fields.ai.use'],
    component: lazy(() => import('./pages/DynamicFieldsPage')),
  },
  {
    path: '/dynamic-fields/:id',
    permission: 'dynamic-fields.field.view',
    component: lazy(() => import('./pages/DynamicFieldsDetailPage')),
  },
  {
    path: '/dynamic-fields/manage',
    permission: 'dynamic-fields.ai.use',
    component: lazy(() => import('./pages/DynamicFieldsManagePage')),
  },
];
