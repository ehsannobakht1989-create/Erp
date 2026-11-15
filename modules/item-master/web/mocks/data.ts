import type { Item } from '../types';

const categories = ['ورق فولادی', 'پیچ و مهره', 'کابل برق', 'بسته‌بندی', 'بلبرینگ', 'لوله صنعتی'];
const uoms = ['KG', 'PCS', 'M', 'ROLL', 'BOX'];

export const mockItems: Item[] = Array.from({ length: 200 }, (_, i) => ({
  id: i + 1,
  code: `ITM-${String(i + 1).padStart(4, '0')}`,
  name_fa: `${categories[i % categories.length]} کد ${i + 1}`,
  item_type: i % 3 === 0 ? 'raw_material' : 'consumable',
  base_uom_id: uoms[i % uoms.length],
  status: i % 5 === 0 ? 'draft' : 'active',
}));
