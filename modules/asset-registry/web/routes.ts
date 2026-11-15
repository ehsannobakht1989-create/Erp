import { lazy } from 'react';

export const routes = [
  {
    path: '/asset-registry',
    permission: 'asset-registry.asset.view',
    permissions: ['asset-registry.asset.view', 'asset-registry.asset.create', 'asset-registry.asset.update', 'asset-registry.meter.record'],
    component: lazy(() => import('./pages/AssetRegistryPage')),
  },
  {
    path: '/asset-registry/:id',
    permission: 'asset-registry.asset.view',
    component: lazy(() => import('./pages/AssetRegistryDetailPage')),
  },
  {
    path: '/asset-registry/manage',
    permission: 'asset-registry.meter.record',
    component: lazy(() => import('./pages/AssetRegistryManagePage')),
  },
];
