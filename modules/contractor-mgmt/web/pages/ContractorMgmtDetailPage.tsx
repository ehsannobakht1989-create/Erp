import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ContractorMgmtDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات مدیریت پیمانکاران" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه مدیریت پیمانکاران.</p>
    </Card>
  </div>
);
export default ContractorMgmtDetailPage;
