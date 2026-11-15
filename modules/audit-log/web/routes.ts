import AuditLogPage from './pages/AuditLogPage';

export default [
  { path: '/audit-log', component: AuditLogPage, permission: 'audit-log.entry.view' }
];
