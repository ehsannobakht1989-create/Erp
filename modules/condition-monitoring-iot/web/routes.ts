import { lazy } from 'react';

export const routes = [
  {
    path: '/condition-monitoring-iot',
    permission: 'condition-monitoring-iot.record.view',
    permissions: ['condition-monitoring-iot.record.view', 'condition-monitoring-iot.record.create', 'condition-monitoring-iot.record.approve'],
    component: lazy(() => import('./pages/ConditionMonitoringIotPage')),
  },
  {
    path: '/condition-monitoring-iot/:id',
    permission: 'condition-monitoring-iot.record.view',
    component: lazy(() => import('./pages/ConditionMonitoringIotDetailPage')),
  },
  {
    path: '/condition-monitoring-iot/manage',
    permission: 'condition-monitoring-iot.record.approve',
    component: lazy(() => import('./pages/ConditionMonitoringIotManagePage')),
  },
];
