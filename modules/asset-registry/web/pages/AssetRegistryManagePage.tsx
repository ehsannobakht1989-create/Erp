import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const AssetRegistryManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات ثبت دارایی‌ها و تجهیزات" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default AssetRegistryManagePage;
