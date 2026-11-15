import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ParkingVehiclesDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات پارکینگ و خودروها" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه پارکینگ و خودروها.</p>
    </Card>
  </div>
);
export default ParkingVehiclesDetailPage;
