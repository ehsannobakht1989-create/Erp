import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const AttendanceDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات حضور و غیاب" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه حضور و غیاب.</p>
    </Card>
  </div>
);
export default AttendanceDetailPage;
