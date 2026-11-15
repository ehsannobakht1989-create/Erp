import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const ToolingManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات ابزار و قالب" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default ToolingManagePage;
