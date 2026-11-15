import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const MpsDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات برنامه‌ریزی جامع تولید" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه برنامه‌ریزی جامع تولید.</p>
    </Card>
  </div>
);
export default MpsDetailPage;
