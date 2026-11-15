import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const DocOcrDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات نویسه‌خوان اسناد" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه نویسه‌خوان اسناد.</p>
    </Card>
  </div>
);
export default DocOcrDetailPage;
