export interface Party {
  id: number;
  code: string;
  name_fa: string;
  party_type: 'customer' | 'supplier' | 'both' | 'contractor' | 'other';
  status: string;
}
