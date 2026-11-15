import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const MrpDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات برنامه‌ریزی نیازمندی مواد" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه برنامه‌ریزی نیازمندی مواد.</p>
    </Card>
  </div>
);
export default MrpDetailPage;
