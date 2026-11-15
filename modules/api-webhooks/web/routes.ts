import ApiWebhooksPage from './pages/ApiWebhooksPage';

export default [
  { path: '/api-webhooks', component: ApiWebhooksPage, permission: 'api-webhooks.key.view' }
];
