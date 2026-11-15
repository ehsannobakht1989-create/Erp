import { lazy } from 'react';

export const routes = [
  {
    path: '/audit-iso',
    permission: 'audit-iso.record.view',
    permissions: ['audit-iso.record.view', 'audit-iso.record.create', 'audit-iso.record.approve'],
    component: lazy(() => import('./pages/AuditIsoPage')),
  },
  {
    path: '/audit-iso/:id',
    permission: 'audit-iso.record.view',
    component: lazy(() => import('./pages/AuditIsoDetailPage')),
  },
  {
    path: '/audit-iso/manage',
    permission: 'audit-iso.record.approve',
    component: lazy(() => import('./pages/AuditIsoManagePage')),
  },
];
