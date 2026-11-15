export const mockUsers = [
  { id: 1, username: 'admin', full_name: 'مدیر سیستم', email: 'admin@erp.local', status: 'active', roles: ['super-admin'] },
  { id: 2, username: 'operator1', full_name: 'اپراتور انبار', email: 'op@erp.local', status: 'active', roles: ['operator'] }
];

export const mockRoles = [
  { id: 1, slug: 'super-admin', name: 'مدیر ارشد', data_scope: 'all', permissions: ['*'] },
  { id: 2, slug: 'operator', name: 'اپراتور', data_scope: 'own', permissions: ['inventory.receipt.view'] }
];
