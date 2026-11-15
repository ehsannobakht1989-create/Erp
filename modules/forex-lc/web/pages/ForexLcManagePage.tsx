import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const ForexLcManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات ارز و اعتبار اسنادی" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default ForexLcManagePage;
