import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const FixedAssetsDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات دارایی‌های ثابت" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه دارایی‌های ثابت.</p>
    </Card>
  </div>
);
export default FixedAssetsDetailPage;
