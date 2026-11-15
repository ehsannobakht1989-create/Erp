import { lazy } from 'react';

export const routes = [
  {
    path: '/org-structure',
    permission: 'org-structure.unit.view',
    permissions: ['org-structure.unit.view', 'org-structure.unit.manage', 'org-structure.cost-center.view', 'org-structure.cost-center.manage', 'org-structure.calendar.manage'],
    component: lazy(() => import('./pages/OrgStructurePage')),
  },
  {
    path: '/org-structure/:id',
    permission: 'org-structure.unit.view',
    component: lazy(() => import('./pages/OrgStructureDetailPage')),
  },
  {
    path: '/org-structure/manage',
    permission: 'org-structure.calendar.manage',
    component: lazy(() => import('./pages/OrgStructureManagePage')),
  },
];
