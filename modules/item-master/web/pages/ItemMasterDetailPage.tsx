import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ItemMasterDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات اطلاعات پایه کالا" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه اطلاعات پایه کالا.</p>
    </Card>
  </div>
);
export default ItemMasterDetailPage;
