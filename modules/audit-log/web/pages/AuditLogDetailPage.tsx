import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const AuditLogDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات رویداد ممیزی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش تغییرات قبل و بعد، کاربر و آدرس IP.</p>
    </Card>
  </div>
);
export default AuditLogDetailPage;
