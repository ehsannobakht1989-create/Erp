import { lazy } from 'react';

export const routes = [
  {
    path: '/quality-inspection',
    permission: 'quality-inspection.record.view',
    permissions: ['quality-inspection.record.view', 'quality-inspection.record.create', 'quality-inspection.record.approve'],
    component: lazy(() => import('./pages/QualityInspectionPage')),
  },
  {
    path: '/quality-inspection/:id',
    permission: 'quality-inspection.record.view',
    component: lazy(() => import('./pages/QualityInspectionDetailPage')),
  },
  {
    path: '/quality-inspection/manage',
    permission: 'quality-inspection.record.approve',
    component: lazy(() => import('./pages/QualityInspectionManagePage')),
  },
];
