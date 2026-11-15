import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const NotificationsManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات اعلان‌ها" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default NotificationsManagePage;
