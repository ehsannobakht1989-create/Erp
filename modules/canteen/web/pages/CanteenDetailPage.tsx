import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const CanteenDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات رستوران و تغذیه" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه رستوران و تغذیه.</p>
    </Card>
  </div>
);
export default CanteenDetailPage;
