import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const ScrapByproductManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات ضایعات و محصولات فرعی" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default ScrapByproductManagePage;
