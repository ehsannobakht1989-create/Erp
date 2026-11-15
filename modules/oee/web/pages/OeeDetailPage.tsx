import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const OeeDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات اثربخشی کلی تجهیزات" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه اثربخشی کلی تجهیزات.</p>
    </Card>
  </div>
);
export default OeeDetailPage;
