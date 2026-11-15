import { useApi } from '@erp/hooks';
import type { Item } from '../types';

export function useItems(query = '') {
  return useApi<Item[]>(`/api/item-master/items?q=${encodeURIComponent(query)}`);
}
