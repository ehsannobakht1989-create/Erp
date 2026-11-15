#!/usr/bin/env bash
set -euo pipefail

FILE_COUNT=$(git ls-files | wc -l | tr -d ' ')
if [ "$FILE_COUNT" -eq 0 ]; then
  TOTAL_BYTES=0
else
  TOTAL_BYTES=$(git ls-files -z | xargs -0 du -cb | tail -1 | awk '{print $1}')
fi
TOTAL_MB=$(awk "BEGIN {printf \"%.2f\", $TOTAL_BYTES / (1024 * 1024)}")

echo "Tracked files: $FILE_COUNT (limit: 7000)"
echo "Tracked size:  ${TOTAL_MB} MB (limit: 90.00 MB)"

# Warn if any module exceeds 50 files
if [ -d "modules" ]; then
  for mod_dir in modules/*/; do
    [ -d "$mod_dir" ] || continue
    mod_files=$(find "$mod_dir" -type f | wc -l | tr -d ' ')
    if [ "$mod_files" -gt 50 ]; then
      echo "WARNING: $mod_dir has $mod_files files (target <= 50)"
    fi
  done
fi

FAILED=0
if [ "$FILE_COUNT" -gt 7000 ]; then
  echo "ERROR: Tracked file count ($FILE_COUNT) exceeds hard limit of 7000."
  FAILED=1
fi

MAX_BYTES=$((90 * 1024 * 1024))
if [ "$TOTAL_BYTES" -gt "$MAX_BYTES" ]; then
  echo "ERROR: Tracked size (${TOTAL_MB} MB) exceeds hard limit of 90 MB."
  FAILED=1
fi

if [ "$FAILED" -ne 0 ]; then
  exit 1
fi

echo "Budget check passed."
