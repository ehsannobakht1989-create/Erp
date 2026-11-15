import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ToolingDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات ابزار و قالب" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه ابزار و قالب.</p>
    </Card>
  </div>
);
export default ToolingDetailPage;
