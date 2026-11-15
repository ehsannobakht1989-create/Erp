import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const FormBuilderDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات فرم‌ساز" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه فرم‌ساز.</p>
    </Card>
  </div>
);
export default FormBuilderDetailPage;
