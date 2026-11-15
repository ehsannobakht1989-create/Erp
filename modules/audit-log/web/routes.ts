import { lazy } from 'react';

export const routes = [
  {
    path: '/audit-log',
    permission: 'audit-log.entry.view',
    component: lazy(() => import('./pages/AuditLogPage')),
  },
  {
    path: '/audit-log/:id',
    permission: 'audit-log.entry.view',
    component: lazy(() => import('./pages/AuditLogDetailPage')),
  },
  {
    path: '/audit-log/export',
    permission: 'audit-log.entry.export',
    aiPermission: 'audit-log.ai.use',
    component: lazy(() => import('./pages/AuditLogExportPage')),
  },
];
