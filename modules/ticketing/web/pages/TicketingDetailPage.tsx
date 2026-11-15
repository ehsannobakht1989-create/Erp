import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const TicketingDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات تیکتینگ و پشتیبانی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه تیکتینگ و پشتیبانی.</p>
    </Card>
  </div>
);
export default TicketingDetailPage;
