import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const FailurePredictionDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات پیش‌بینی خرابی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه پیش‌بینی خرابی.</p>
    </Card>
  </div>
);
export default FailurePredictionDetailPage;
