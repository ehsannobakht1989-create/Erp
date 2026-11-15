import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const QualityInspectionDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات کنترل کیفیت" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه کنترل کیفیت.</p>
    </Card>
  </div>
);
export default QualityInspectionDetailPage;
