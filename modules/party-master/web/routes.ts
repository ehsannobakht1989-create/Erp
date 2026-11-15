import { lazy } from 'react';

export const routes = [
  {
    path: '/party-master',
    permission: 'party-master.party.view',
    permissions: ['party-master.party.view', 'party-master.party.create', 'party-master.party.update', 'party-master.party.approve', 'party-master.party.delete', 'party-master.party.merge', 'party-master.party.import', 'party-master.party.export', 'party-master.group.manage', 'party-master.payment-term.manage', 'party-master.ai.use'],
    component: lazy(() => import('./pages/PartyMasterPage')),
  },
  {
    path: '/party-master/:id',
    permission: 'party-master.party.view',
    component: lazy(() => import('./pages/PartyMasterDetailPage')),
  },
  {
    path: '/party-master/manage',
    permission: 'party-master.ai.use',
    component: lazy(() => import('./pages/PartyMasterManagePage')),
  },
];
