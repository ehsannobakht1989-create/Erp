import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const RoutingDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات مسیر تولید" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه مسیر تولید.</p>
    </Card>
  </div>
);
export default RoutingDetailPage;
