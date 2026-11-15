import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const PartyMasterDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات اشخاص و طرف‌حساب‌ها" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه اشخاص و طرف‌حساب‌ها.</p>
    </Card>
  </div>
);
export default PartyMasterDetailPage;
