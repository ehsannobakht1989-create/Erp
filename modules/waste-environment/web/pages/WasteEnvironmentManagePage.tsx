import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const WasteEnvironmentManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات پسماند و محیط زیست" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default WasteEnvironmentManagePage;
