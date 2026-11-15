export type ItemStatus = 'draft' | 'pending_approval' | 'active' | 'on_hold' | 'obsolete';

export interface Item {
  id: number;
  code: string;
  name_fa: string;
  item_type: string;
  base_uom_id: string;
  status: ItemStatus;
}
