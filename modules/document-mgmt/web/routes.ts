import DocumentMgmtPage from './pages/DocumentMgmtPage';

export default [
  { path: '/document-mgmt', component: DocumentMgmtPage, permission: 'document-mgmt.document.view' }
];
