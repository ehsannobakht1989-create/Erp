import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const AnomalyDetectionManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات تشخیص ناهنجاری" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default AnomalyDetectionManagePage;
