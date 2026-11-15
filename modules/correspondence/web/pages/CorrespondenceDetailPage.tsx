import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const CorrespondenceDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات دبیرخانه و مکاتبات" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه دبیرخانه و مکاتبات.</p>
    </Card>
  </div>
);
export default CorrespondenceDetailPage;
