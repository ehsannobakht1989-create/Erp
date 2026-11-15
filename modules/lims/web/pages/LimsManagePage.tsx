import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const LimsManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات مدیریت آزمایشگاه" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default LimsManagePage;
