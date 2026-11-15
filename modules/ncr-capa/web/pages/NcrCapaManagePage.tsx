import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const NcrCapaManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات عدم انطباق و اقدام اصلاحی" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default NcrCapaManagePage;
