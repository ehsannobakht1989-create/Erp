import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const PricingManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات قیمت‌گذاری" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default PricingManagePage;
