import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ManagementConsolidationDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات تلفیق مدیریتی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه تلفیق مدیریتی.</p>
    </Card>
  </div>
);
export default ManagementConsolidationDetailPage;
