import { lazy } from 'react';

export const routes = [
  {
    path: '/competency-appraisal',
    permission: 'competency-appraisal.record.view',
    permissions: ['competency-appraisal.record.view', 'competency-appraisal.record.create', 'competency-appraisal.record.approve'],
    component: lazy(() => import('./pages/CompetencyAppraisalPage')),
  },
  {
    path: '/competency-appraisal/:id',
    permission: 'competency-appraisal.record.view',
    component: lazy(() => import('./pages/CompetencyAppraisalDetailPage')),
  },
  {
    path: '/competency-appraisal/manage',
    permission: 'competency-appraisal.record.approve',
    component: lazy(() => import('./pages/CompetencyAppraisalManagePage')),
  },
];
