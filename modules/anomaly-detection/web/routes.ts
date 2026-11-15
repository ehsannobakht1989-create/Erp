import { lazy } from 'react';

export const routes = [
  {
    path: '/anomaly-detection',
    permission: 'anomaly-detection.record.view',
    permissions: ['anomaly-detection.record.view', 'anomaly-detection.record.create', 'anomaly-detection.record.approve'],
    component: lazy(() => import('./pages/AnomalyDetectionPage')),
  },
  {
    path: '/anomaly-detection/:id',
    permission: 'anomaly-detection.record.view',
    component: lazy(() => import('./pages/AnomalyDetectionDetailPage')),
  },
  {
    path: '/anomaly-detection/manage',
    permission: 'anomaly-detection.record.approve',
    component: lazy(() => import('./pages/AnomalyDetectionManagePage')),
  },
];
