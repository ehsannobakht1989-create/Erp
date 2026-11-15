import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const AuthRbacManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات احراز هویت و دسترسی" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default AuthRbacManagePage;
