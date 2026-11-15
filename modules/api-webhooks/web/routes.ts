import { lazy } from 'react';

export const routes = [
  {
    path: '/api-webhooks',
    permission: 'api-webhooks.key.view',
    permissions: ['api-webhooks.key.view', 'api-webhooks.key.manage', 'api-webhooks.webhook.manage', 'api-webhooks.ai.use'],
    component: lazy(() => import('./pages/ApiWebhooksPage')),
  },
  {
    path: '/api-webhooks/:id',
    permission: 'api-webhooks.key.view',
    component: lazy(() => import('./pages/ApiWebhooksDetailPage')),
  },
  {
    path: '/api-webhooks/manage',
    permission: 'api-webhooks.ai.use',
    component: lazy(() => import('./pages/ApiWebhooksManagePage')),
  },
];
