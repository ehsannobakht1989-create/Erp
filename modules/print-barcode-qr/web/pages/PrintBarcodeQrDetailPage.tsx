import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const PrintBarcodeQrDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات چاپ و بارکد" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه چاپ و بارکد.</p>
    </Card>
  </div>
);
export default PrintBarcodeQrDetailPage;
