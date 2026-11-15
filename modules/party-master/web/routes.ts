import PartyMasterPage from './pages/PartyMasterPage';

export default [
  { path: '/party-master', component: PartyMasterPage, permission: 'party-master.party.view' }
];
