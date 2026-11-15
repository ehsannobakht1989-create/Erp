import NotificationsPage from './pages/NotificationsPage';

export default [
  { path: '/notifications', component: NotificationsPage, permission: 'notifications.message.view' }
];
