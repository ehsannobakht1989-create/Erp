import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const DemandForecastManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات پیش‌بینی تقاضا" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default DemandForecastManagePage;
