import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const BomDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات ساختار محصول" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه ساختار محصول.</p>
    </Card>
  </div>
);
export default BomDetailPage;
