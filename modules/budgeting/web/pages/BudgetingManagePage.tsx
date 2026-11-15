import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const BudgetingManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات بودجه‌بندی" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default BudgetingManagePage;
