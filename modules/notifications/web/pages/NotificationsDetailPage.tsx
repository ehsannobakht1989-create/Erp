import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const NotificationsDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات اعلان‌ها" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه اعلان‌ها.</p>
    </Card>
  </div>
);
export default NotificationsDetailPage;
