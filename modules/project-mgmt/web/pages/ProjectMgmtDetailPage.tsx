import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ProjectMgmtDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات مدیریت پروژه" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه مدیریت پروژه.</p>
    </Card>
  </div>
);
export default ProjectMgmtDetailPage;
