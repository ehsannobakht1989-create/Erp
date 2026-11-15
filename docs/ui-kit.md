# `@erp/ui` & `@erp/hooks` Reference

## Components (`@erp/ui`)
- `Button`: `<Button variant="primary" onClick={...}>ذخیره</Button>`
- `Input`: `<Input label="نام کالا" error={errors.name} />`
- `Card`: `<Card title="اطلاعات پایه">...</Card>`
- `Badge`: `<Badge tone="green">تایید شده</Badge>`
- `EmptyState`: `<EmptyState title="خالی" />`
- `DataTable`: `<DataTable columns={[{key:'id',label:'شناسه'}]} rows={rows} />`
- `AiButton`: `<AiButton capability="summarize" enabled={aiEnabled} />`
- `AiProposalCard`: `<AiProposalCard title="..." onApprove={...} onReject={...} />`

## Hooks (`@erp/hooks`)
- `useApi<T>(url)`: fetches `/api/...` with `{ok, data, error, meta}` envelope.
- `usePermission(perm, userPerms)`: checks permission string.
- `useModuleEnabled(id, enabledModules)`: checks if a module is enabled.
- `useNotify()`: toast notifications.

## i18n (`@erp/i18n`)
- `t('inventory.title')`: looks up `modules/inventory/web/i18n/fa.json`.
