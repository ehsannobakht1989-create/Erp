import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const ReportBiManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات گزارش‌ساز و هوش تجاری" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default ReportBiManagePage;
