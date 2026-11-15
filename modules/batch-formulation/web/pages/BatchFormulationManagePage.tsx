import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const BatchFormulationManagePage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="مدیریت و تنظیمات فرمولاسیون و بچ" />
    <Card>
      <Button>ذخیره تنظیمات</Button>
    </Card>
  </div>
);
export default BatchFormulationManagePage;
