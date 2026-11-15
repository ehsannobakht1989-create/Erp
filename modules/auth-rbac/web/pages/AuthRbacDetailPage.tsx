import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const AuthRbacDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات احراز هویت و دسترسی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه احراز هویت و دسترسی.</p>
    </Card>
  </div>
);
export default AuthRbacDetailPage;
