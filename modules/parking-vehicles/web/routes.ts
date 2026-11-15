import { lazy } from 'react';

export const routes = [
  {
    path: '/parking-vehicles',
    permission: 'parking-vehicles.record.view',
    permissions: ['parking-vehicles.record.view', 'parking-vehicles.record.create', 'parking-vehicles.record.approve'],
    component: lazy(() => import('./pages/ParkingVehiclesPage')),
  },
  {
    path: '/parking-vehicles/:id',
    permission: 'parking-vehicles.record.view',
    component: lazy(() => import('./pages/ParkingVehiclesDetailPage')),
  },
  {
    path: '/parking-vehicles/manage',
    permission: 'parking-vehicles.record.approve',
    component: lazy(() => import('./pages/ParkingVehiclesManagePage')),
  },
];
