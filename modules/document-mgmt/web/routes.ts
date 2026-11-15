import { lazy } from 'react';

export const routes = [
  {
    path: '/document-mgmt',
    permission: 'document-mgmt.document.view',
    permissions: ['document-mgmt.document.view', 'document-mgmt.document.upload', 'document-mgmt.ai.use'],
    component: lazy(() => import('./pages/DocumentMgmtPage')),
  },
  {
    path: '/document-mgmt/:id',
    permission: 'document-mgmt.document.view',
    component: lazy(() => import('./pages/DocumentMgmtDetailPage')),
  },
  {
    path: '/document-mgmt/manage',
    permission: 'document-mgmt.ai.use',
    component: lazy(() => import('./pages/DocumentMgmtManagePage')),
  },
];
