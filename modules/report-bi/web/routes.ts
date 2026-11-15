import { lazy } from 'react';

export const routes = [
  {
    path: '/report-bi',
    permission: 'report-bi.record.view',
    permissions: ['report-bi.record.view', 'report-bi.record.create', 'report-bi.record.approve'],
    component: lazy(() => import('./pages/ReportBiPage')),
  },
  {
    path: '/report-bi/:id',
    permission: 'report-bi.record.view',
    component: lazy(() => import('./pages/ReportBiDetailPage')),
  },
  {
    path: '/report-bi/manage',
    permission: 'report-bi.record.approve',
    component: lazy(() => import('./pages/ReportBiManagePage')),
  },
];
