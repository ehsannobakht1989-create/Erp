import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const AnomalyDetectionDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات تشخیص ناهنجاری" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه تشخیص ناهنجاری.</p>
    </Card>
  </div>
);
export default AnomalyDetectionDetailPage;
