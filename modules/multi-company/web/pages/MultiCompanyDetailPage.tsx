import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const MultiCompanyDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات چندشرکتی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه چندشرکتی.</p>
    </Card>
  </div>
);
export default MultiCompanyDetailPage;
