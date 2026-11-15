import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const OrgStructureDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات ساختار سازمانی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه ساختار سازمانی.</p>
    </Card>
  </div>
);
export default OrgStructureDetailPage;
