# pricing — Price lists & discount engine (L2; requires item-master, party-master)

**Entities** PriceList{code,type[sales|purchase],currency,valid_from,valid_to,priority,party_group_id?,party_id?,status}; PriceListLine{item_id|category_id,uom,min_qty,price,discount_pct,valid_from,valid_to}; DiscountRule{name,scope[line|header],type[percent|amount|free_item],conditions(json: qty, amount, item/category, party group, date),priority,stackable,max_discount}; Campaign{name,from,to,rules[]}; PriceChangeBatch{lines,effective_date,status}.
**Rules** resolution order: party-specific > group > default list; tiers by min_qty; validity windows no overlap per (list,item,uom,min_qty); deterministic priority + stackable flag; margin guard against cost if CostingService available (setting: warn|block); price changes via approval workflow; history kept.
**Events out** pricing.pricelist.activated, pricing.price.changed. **In** costing.cost.updated (optional, margin alerts).
**Service contract** PricingService: resolve(PriceContext{type,party,item,uom,qty,date,currency,docRef}) → PriceResult{price,discounts[],source,explain[]}, resolveBulk(), explain(), activeLists(party).
**Permissions** pricing.list.view|manage|approve, pricing.rule.manage, pricing.campaign.manage, pricing.override.
**Screens** price lists, grid editor (item × tier), rule builder (conditions UI), campaigns, mass update (% / formula, preview diff), price history, price simulator ("what price would party X get for item Y, qty N").
**AI** autofill (suggest list from cost + target margin, proposal), anomaly (outlier prices, expired lists in use), summarize price changes.
**Iran** rial/toman display setting, multi-currency for imports, VAT handled by sales not pricing.
