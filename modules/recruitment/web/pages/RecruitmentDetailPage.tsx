import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const RecruitmentDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات استخدام" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه استخدام.</p>
    </Card>
  </div>
);
export default RecruitmentDetailPage;
