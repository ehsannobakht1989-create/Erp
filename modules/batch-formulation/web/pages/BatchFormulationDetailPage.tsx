import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const BatchFormulationDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات فرمولاسیون و بچ" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه فرمولاسیون و بچ.</p>
    </Card>
  </div>
);
export default BatchFormulationDetailPage;
