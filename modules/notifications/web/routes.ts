import { lazy } from 'react';

export const routes = [
  {
    path: '/notifications',
    permission: 'notifications.message.view',
    permissions: ['notifications.message.view', 'notifications.template.manage', 'notifications.ai.use'],
    component: lazy(() => import('./pages/NotificationsPage')),
  },
  {
    path: '/notifications/:id',
    permission: 'notifications.message.view',
    component: lazy(() => import('./pages/NotificationsDetailPage')),
  },
  {
    path: '/notifications/manage',
    permission: 'notifications.ai.use',
    component: lazy(() => import('./pages/NotificationsManagePage')),
  },
];
