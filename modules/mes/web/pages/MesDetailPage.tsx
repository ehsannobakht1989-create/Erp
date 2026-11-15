import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const MesDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات اجرای تولید" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه اجرای تولید.</p>
    </Card>
  </div>
);
export default MesDetailPage;
