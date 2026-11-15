import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const TaxMoadianManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات مالیات و سامانه مودیان" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default TaxMoadianManagePage;
