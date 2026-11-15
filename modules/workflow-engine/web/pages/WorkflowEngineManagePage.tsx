import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const WorkflowEngineManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات موتور گردش کار" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default WorkflowEngineManagePage;
