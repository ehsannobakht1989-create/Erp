import React from 'react';
import { mockUsers, mockRoles } from '../mocks/data';

export default function AuthRbacPage() {
  return (
    <div dir="rtl" className="p-6 space-y-6">
      <h1 className="text-2xl font-bold">کاربران و دسترسی‌ها</h1>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="border rounded p-4">
          <h2 className="font-semibold mb-2">کاربران ({mockUsers.length})</h2>
          <ul className="space-y-1 text-sm">
            {mockUsers.map((u) => (
              <li key={u.id}>{u.full_name} (@{u.username})</li>
            ))}
          </ul>
        </div>
        <div className="border rounded p-4">
          <h2 className="font-semibold mb-2">نقش‌ها ({mockRoles.length})</h2>
          <ul className="space-y-1 text-sm">
            {mockRoles.map((r) => (
              <li key={r.id}>{r.name} ({r.data_scope})</li>
            ))}
          </ul>
        </div>
      </div>
    </div>
  );
}
