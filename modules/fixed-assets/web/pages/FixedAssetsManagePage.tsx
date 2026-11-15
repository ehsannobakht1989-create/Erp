import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const FixedAssetsManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات دارایی‌های ثابت" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default FixedAssetsManagePage;
