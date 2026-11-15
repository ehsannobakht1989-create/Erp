import { lazy } from 'react';

export const routes = [
  {
    path: '/doc-ocr',
    permission: 'doc-ocr.record.view',
    permissions: ['doc-ocr.record.view', 'doc-ocr.record.create', 'doc-ocr.record.approve'],
    component: lazy(() => import('./pages/DocOcrPage')),
  },
  {
    path: '/doc-ocr/:id',
    permission: 'doc-ocr.record.view',
    component: lazy(() => import('./pages/DocOcrDetailPage')),
  },
  {
    path: '/doc-ocr/manage',
    permission: 'doc-ocr.record.approve',
    component: lazy(() => import('./pages/DocOcrManagePage')),
  },
];
