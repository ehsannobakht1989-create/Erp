import { lazy } from 'react';

export const routes = [
  {
    path: '/print-barcode-qr',
    permission: 'print-barcode-qr.template.view',
    permissions: ['print-barcode-qr.template.view', 'print-barcode-qr.template.manage', 'print-barcode-qr.ai.use'],
    component: lazy(() => import('./pages/PrintBarcodeQrPage')),
  },
  {
    path: '/print-barcode-qr/:id',
    permission: 'print-barcode-qr.template.view',
    component: lazy(() => import('./pages/PrintBarcodeQrDetailPage')),
  },
  {
    path: '/print-barcode-qr/manage',
    permission: 'print-barcode-qr.ai.use',
    component: lazy(() => import('./pages/PrintBarcodeQrManagePage')),
  },
];
