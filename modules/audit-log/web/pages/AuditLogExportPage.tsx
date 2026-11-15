import React from 'react';
import { PageHeader, Card, Button } from '@erp/ui';

export const AuditLogExportPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="خروجی و تحلیل هوشمند ممیزی" />
    <Card>
      <div className="flex gap-2">
        <Button>خروجی JSON/CSV</Button>
        <Button variant="secondary">خلاصه‌سازی هوشمند</Button>
      </div>
    </Card>
  </div>
);
export default AuditLogExportPage;
