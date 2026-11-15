import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const EnergyUtilitiesDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات انرژی و تاسیسات" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه انرژی و تاسیسات.</p>
    </Card>
  </div>
);
export default EnergyUtilitiesDetailPage;
