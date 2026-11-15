import { lazy } from 'react';

export const routes = [
  {
    path: '/form-builder',
    permission: 'form-builder.form.view',
    permissions: ['form-builder.form.view', 'form-builder.form.manage', 'form-builder.submission.create', 'form-builder.ai.use'],
    component: lazy(() => import('./pages/FormBuilderPage')),
  },
  {
    path: '/form-builder/:id',
    permission: 'form-builder.form.view',
    component: lazy(() => import('./pages/FormBuilderDetailPage')),
  },
  {
    path: '/form-builder/manage',
    permission: 'form-builder.ai.use',
    component: lazy(() => import('./pages/FormBuilderManagePage')),
  },
];
