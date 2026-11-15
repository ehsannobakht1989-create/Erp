import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const MpsManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات برنامه‌ریزی جامع تولید" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default MpsManagePage;
