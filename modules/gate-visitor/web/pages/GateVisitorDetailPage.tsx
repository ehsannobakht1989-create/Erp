import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const GateVisitorDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات نگهبانی و مراجعین" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه نگهبانی و مراجعین.</p>
    </Card>
  </div>
);
export default GateVisitorDetailPage;
