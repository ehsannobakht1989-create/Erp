import { lazy } from 'react';

export const routes = [
  {
    path: '/workflow-engine',
    permission: 'workflow-engine.task.view',
    permissions: ['workflow-engine.task.view', 'workflow-engine.task.act', 'workflow-engine.definition.manage', 'workflow-engine.ai.use'],
    component: lazy(() => import('./pages/WorkflowEnginePage')),
  },
  {
    path: '/workflow-engine/:id',
    permission: 'workflow-engine.task.view',
    component: lazy(() => import('./pages/WorkflowEngineDetailPage')),
  },
  {
    path: '/workflow-engine/manage',
    permission: 'workflow-engine.ai.use',
    component: lazy(() => import('./pages/WorkflowEngineManagePage')),
  },
];
