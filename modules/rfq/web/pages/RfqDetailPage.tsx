import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const RfqDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات استعلام بها" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه استعلام بها.</p>
    </Card>
  </div>
);
export default RfqDetailPage;
