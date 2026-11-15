import { describe, it, expect } from 'vitest';
import { usePermission } from '../src/hooks';

describe('Shell hooks', () => {
  it('checks permissions accurately', () => {
    expect(usePermission('inventory.receipt.view', ['*'])).toBe(true);
    expect(usePermission('inventory.receipt.view', ['inventory.receipt.view'])).toBe(true);
    expect(usePermission('inventory.receipt.approve', ['inventory.receipt.view'])).toBe(false);
  });
});
