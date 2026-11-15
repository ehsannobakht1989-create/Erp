import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ContractsDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات مدیریت قراردادها" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه مدیریت قراردادها.</p>
    </Card>
  </div>
);
export default ContractsDetailPage;
