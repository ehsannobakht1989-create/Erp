import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ImportCustomsDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات واردات و گمرک" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه واردات و گمرک.</p>
    </Card>
  </div>
);
export default ImportCustomsDetailPage;
