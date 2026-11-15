import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const TreasuryDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات خزانه‌داری" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه خزانه‌داری.</p>
    </Card>
  </div>
);
export default TreasuryDetailPage;
