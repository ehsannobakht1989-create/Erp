import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const CompetencyAppraisalDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات ارزیابی شایستگی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه ارزیابی شایستگی.</p>
    </Card>
  </div>
);
export default CompetencyAppraisalDetailPage;
