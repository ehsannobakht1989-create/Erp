import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ScrapByproductDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات ضایعات و محصولات فرعی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه ضایعات و محصولات فرعی.</p>
    </Card>
  </div>
);
export default ScrapByproductDetailPage;
