import { useState, useEffect, useCallback } from 'react';

export interface ApiEnvelope<T = unknown> {
  ok: boolean;
  data: T | null;
  error: { code: string; message: string; details?: Record<string, unknown> } | null;
  meta: Record<string, unknown>;
}

export function useApi<T = unknown>(url: string) {
  const [data, setData] = useState<T | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchNow = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const token = localStorage.getItem('erp_token');
      const companyId = localStorage.getItem('erp_company_id') || '1';
      const res = await fetch(url, {
        headers: {
          'Content-Type': 'application/json',
          'X-Company-Id': companyId,
          ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
      });
      const json = (await res.json()) as ApiEnvelope<T>;
      if (!json.ok) {
        setError(json.error?.message || 'خطا در ارتباط با سرور');
      } else {
        setData(json.data);
      }
    } catch (e) {
      setError(e instanceof Error ? e.message : 'Network error');
    } finally {
      setLoading(false);
    }
  }, [url]);

  useEffect(() => {
    fetchNow();
  }, [fetchNow]);

  return { data, loading, error, refetch: fetchNow };
}

export function usePermission(permission: string, userPermissions: string[] = ['*']): boolean {
  return userPermissions.includes('*') || userPermissions.includes(permission);
}

export function useModuleEnabled(moduleId: string, enabledModules: string[] = []): boolean {
  return enabledModules.includes(moduleId);
}

export function useJalali(gregorianDate: string): string {
  if (!gregorianDate) return '';
  return gregorianDate;
}

export function useNotify() {
  return {
    success: (msg: string) => console.info('[SUCCESS]', msg),
    error: (msg: string) => console.error('[ERROR]', msg),
  };
}
