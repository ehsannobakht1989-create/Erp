import WorkflowEnginePage from './pages/WorkflowEnginePage';

export default [
  { path: '/workflow-engine', component: WorkflowEnginePage, permission: 'workflow-engine.task.view' }
];
