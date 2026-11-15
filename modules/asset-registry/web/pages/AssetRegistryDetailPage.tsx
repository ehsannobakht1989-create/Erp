import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const AssetRegistryDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات ثبت دارایی‌ها و تجهیزات" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه ثبت دارایی‌ها و تجهیزات.</p>
    </Card>
  </div>
);
export default AssetRegistryDetailPage;
