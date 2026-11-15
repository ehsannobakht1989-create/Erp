import { lazy } from 'react';

export const routes = [
  {
    path: '/nl-report-assistant',
    permission: 'nl-report-assistant.record.view',
    permissions: ['nl-report-assistant.record.view', 'nl-report-assistant.record.create', 'nl-report-assistant.record.approve'],
    component: lazy(() => import('./pages/NlReportAssistantPage')),
  },
  {
    path: '/nl-report-assistant/:id',
    permission: 'nl-report-assistant.record.view',
    component: lazy(() => import('./pages/NlReportAssistantDetailPage')),
  },
  {
    path: '/nl-report-assistant/manage',
    permission: 'nl-report-assistant.record.approve',
    component: lazy(() => import('./pages/NlReportAssistantManagePage')),
  },
];
