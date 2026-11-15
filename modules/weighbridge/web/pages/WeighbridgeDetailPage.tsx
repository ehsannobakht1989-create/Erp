import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const WeighbridgeDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات باسکول" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه باسکول.</p>
    </Card>
  </div>
);
export default WeighbridgeDetailPage;
