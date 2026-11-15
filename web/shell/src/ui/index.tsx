import React from 'react';

export const Button: React.FC<React.ButtonHTMLAttributes<HTMLButtonElement> & { variant?: 'primary' | 'secondary' | 'danger' }> = ({
  variant = 'primary',
  className = '',
  children,
  ...props
}) => {
  const colors = {
    primary: 'bg-blue-600 text-white hover:bg-blue-700',
    secondary: 'bg-slate-200 text-slate-800 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-100',
    danger: 'bg-red-600 text-white hover:bg-red-700',
  }[variant];
  return (
    <button className={`px-3 py-1.5 rounded text-sm font-medium transition ${colors} ${className}`} {...props}>
      {children}
    </button>
  );
};

export const Input: React.FC<React.InputHTMLAttributes<HTMLInputElement> & { label?: string; error?: string }> = ({
  label,
  error,
  className = '',
  ...props
}) => (
  <div className="space-y-1">
    {label && <label className="block text-sm font-medium">{label}</label>}
    <input className={`border rounded px-2.5 py-1.5 text-sm w-full dark:bg-slate-900 dark:border-slate-700 ${className}`} {...props} />
    {error && <p className="text-xs text-red-600">{error}</p>}
  </div>
);

export const Card: React.FC<{ title?: string; children: React.ReactNode; className?: string }> = ({ title, children, className = '' }) => (
  <div className={`border rounded-lg p-4 bg-white dark:bg-slate-900 dark:border-slate-800 shadow-sm ${className}`}>
    {title && <h3 className="font-semibold mb-3 text-base">{title}</h3>}
    {children}
  </div>
);

export const Badge: React.FC<{ children: React.ReactNode; tone?: 'gray' | 'green' | 'amber' | 'red' }> = ({ children, tone = 'gray' }) => {
  const cls = {
    gray: 'bg-slate-100 text-slate-700',
    green: 'bg-emerald-100 text-emerald-800',
    amber: 'bg-amber-100 text-amber-800',
    red: 'bg-red-100 text-red-800',
  }[tone];
  return <span className={`inline-block px-2 py-0.5 rounded text-xs font-medium ${cls}`}>{children}</span>;
};

export const EmptyState: React.FC<{ title?: string; description?: string }> = ({
  title = 'موردی یافت نشد',
  description = 'داده‌ای برای نمایش وجود ندارد.',
}) => (
  <div className="text-center py-8 text-slate-500">
    <div className="font-semibold">{title}</div>
    <div className="text-sm mt-1">{description}</div>
  </div>
);

export const DataTable: React.FC<{ columns: { key: string; label: string }[]; rows: Record<string, unknown>[] }> = ({ columns, rows }) => (
  <div className="overflow-x-auto border rounded-lg dark:border-slate-800">
    <table className="w-full text-sm text-right">
      <thead className="bg-slate-100 dark:bg-slate-800">
        <tr>
          {columns.map((c) => (
            <th key={c.key} className="px-3 py-2 font-semibold">{c.label}</th>
          ))}
        </tr>
      </thead>
      <tbody>
        {rows.length === 0 ? (
          <tr><td colSpan={columns.length}><EmptyState /></td></tr>
        ) : (
          rows.map((r, i) => (
            <tr key={i} className="border-t dark:border-slate-800">
              {columns.map((c) => (
                <td key={c.key} className="px-3 py-2">{String(r[c.key] ?? '')}</td>
              ))}
            </tr>
          ))
        )}
      </tbody>
    </table>
  </div>
);

export const AiButton: React.FC<{ capability: string; enabled?: boolean; onClick?: () => void }> = ({
  capability,
  enabled = false,
  onClick,
}) => {
  if (!enabled) return null;
  return (
    <Button variant="secondary" onClick={onClick}>
      دستیار هوشمند ({capability})
    </Button>
  );
};

export const AiProposalCard: React.FC<{
  title: string;
  onApprove: () => void;
  onReject: () => void;
}> = ({ title, onApprove, onReject }) => (
  <Card title="پیشنهاد هوش مصنوعی" className="border-amber-300">
    <p className="text-sm mb-3">{title}</p>
    <div className="flex gap-2">
      <Button onClick={onApprove}>تایید</Button>
      <Button variant="danger" onClick={onReject}>رد</Button>
    </div>
  </Card>
);
