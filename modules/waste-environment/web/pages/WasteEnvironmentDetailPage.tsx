import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const WasteEnvironmentDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات پسماند و محیط زیست" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه پسماند و محیط زیست.</p>
    </Card>
  </div>
);
export default WasteEnvironmentDetailPage;
