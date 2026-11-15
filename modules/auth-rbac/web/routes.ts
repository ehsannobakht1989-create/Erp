import AuthRbacPage from './pages/AuthRbacPage';

export default [
  {
    path: '/auth-rbac',
    component: AuthRbacPage,
    permission: 'auth-rbac.user.view',
  },
];
