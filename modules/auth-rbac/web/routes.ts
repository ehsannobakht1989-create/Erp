import { lazy } from 'react';

export const routes = [
  {
    path: '/auth-rbac',
    permission: 'auth-rbac.user.view',
    permissions: ['auth-rbac.user.view', 'auth-rbac.user.create', 'auth-rbac.user.update', 'auth-rbac.role.view', 'auth-rbac.role.manage', 'auth-rbac.ai.use'],
    component: lazy(() => import('./pages/AuthRbacPage')),
  },
  {
    path: '/auth-rbac/:id',
    permission: 'auth-rbac.user.view',
    component: lazy(() => import('./pages/AuthRbacDetailPage')),
  },
  {
    path: '/auth-rbac/manage',
    permission: 'auth-rbac.ai.use',
    component: lazy(() => import('./pages/AuthRbacManagePage')),
  },
];
