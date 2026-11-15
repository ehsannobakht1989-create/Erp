import { lazy } from 'react';

export const routes = [
  {
    path: '/canteen',
    permission: 'canteen.record.view',
    permissions: ['canteen.record.view', 'canteen.record.create', 'canteen.record.approve'],
    component: lazy(() => import('./pages/CanteenPage')),
  },
  {
    path: '/canteen/:id',
    permission: 'canteen.record.view',
    component: lazy(() => import('./pages/CanteenDetailPage')),
  },
  {
    path: '/canteen/manage',
    permission: 'canteen.record.approve',
    component: lazy(() => import('./pages/CanteenManagePage')),
  },
];
