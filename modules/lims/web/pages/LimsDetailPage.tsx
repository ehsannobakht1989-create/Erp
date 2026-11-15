import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const LimsDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات مدیریت آزمایشگاه" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه مدیریت آزمایشگاه.</p>
    </Card>
  </div>
);
export default LimsDetailPage;
