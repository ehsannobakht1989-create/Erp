import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const GateVisitorManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات نگهبانی و مراجعین" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default GateVisitorManagePage;
