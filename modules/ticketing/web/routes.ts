import { lazy } from 'react';

export const routes = [
  {
    path: '/ticketing',
    permission: 'ticketing.record.view',
    permissions: ['ticketing.record.view', 'ticketing.record.create', 'ticketing.record.approve'],
    component: lazy(() => import('./pages/TicketingPage')),
  },
  {
    path: '/ticketing/:id',
    permission: 'ticketing.record.view',
    component: lazy(() => import('./pages/TicketingDetailPage')),
  },
  {
    path: '/ticketing/manage',
    permission: 'ticketing.record.approve',
    component: lazy(() => import('./pages/TicketingManagePage')),
  },
];
