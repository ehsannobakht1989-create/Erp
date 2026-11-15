import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const LotTraceabilityDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات ردیابی بچ و لات" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه ردیابی بچ و لات.</p>
    </Card>
  </div>
);
export default LotTraceabilityDetailPage;
