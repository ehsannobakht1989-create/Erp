import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ForexLcDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات ارز و اعتبار اسنادی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه ارز و اعتبار اسنادی.</p>
    </Card>
  </div>
);
export default ForexLcDetailPage;
