import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const DocumentMgmtDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات مدیریت اسناد" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه مدیریت اسناد.</p>
    </Card>
  </div>
);
export default DocumentMgmtDetailPage;
