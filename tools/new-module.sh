#!/usr/bin/env bash
set -euo pipefail

if [ $# -lt 1 ]; then
  echo "Usage: $0 <module-id> [fa-name] [en-name] [category]"
  exit 1
fi

ID="$1"
FA="${2:-$ID}"
EN="${3:-$ID}"
CAT="${4:-general}"

if ! [[ "$ID" =~ ^[a-z][a-z0-9-]*$ ]]; then
  echo "ERROR: module-id must be kebab-case (e.g. 'inventory-count')."
  exit 1
fi

TARGET="modules/$ID"
if [ -d "$TARGET" ]; then
  echo "ERROR: $TARGET already exists."
  exit 1
fi

cp -r tools/skeleton "$TARGET"

sed -i "s/<MODULE_ID>/$ID/g" "$TARGET/module.json" "$TARGET/README.md"
sed -i "s/<MODULE_FA>/$FA/g" "$TARGET/module.json"
sed -i "s/<MODULE_EN>/$EN/g" "$TARGET/module.json"
sed -i "s/<CATEGORY>/$CAT/g" "$TARGET/module.json"

echo "Created module skeleton at $TARGET"
