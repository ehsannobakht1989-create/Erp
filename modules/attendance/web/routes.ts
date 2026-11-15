import { lazy } from 'react';

export const routes = [
  {
    path: '/attendance',
    permission: 'attendance.record.view',
    permissions: ['attendance.record.view', 'attendance.record.create', 'attendance.record.approve'],
    component: lazy(() => import('./pages/AttendancePage')),
  },
  {
    path: '/attendance/:id',
    permission: 'attendance.record.view',
    component: lazy(() => import('./pages/AttendanceDetailPage')),
  },
  {
    path: '/attendance/manage',
    permission: 'attendance.record.approve',
    component: lazy(() => import('./pages/AttendanceManagePage')),
  },
];
