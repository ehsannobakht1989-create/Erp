import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const AuditIsoDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات ممیزی ایزو" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه ممیزی ایزو.</p>
    </Card>
  </div>
);
export default AuditIsoDetailPage;
