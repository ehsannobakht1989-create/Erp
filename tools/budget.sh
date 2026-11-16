#!/usr/bin/env bash
set -euo pipefail
cd "$(git rev-parse --show-toplevel)"

# Include untracked, non-ignored files as well as tracked files so the check
# cannot pass simply because a large addition has not been staged yet.
git ls-files -z --cached --others --exclude-standard | python3 -c '
import os, sys
paths = set(filter(None, sys.stdin.buffer.read().split(b"\0")))
existing = [p for p in paths if os.path.isfile(p) or os.path.islink(p)]
size = sum(os.lstat(p).st_size for p in existing)
count = len(existing)
print(f"Repository files (tracked + untracked, excluding ignored): {count} (fail at 7000)")
print(f"Repository size: {size / 1048576:.2f} MiB (fail at 90 MiB)")
modules = {}
for path in existing:
    parts = path.split(b"/")
    if len(parts) > 2 and parts[0] == b"modules":
        modules[parts[1]] = modules.get(parts[1], 0) + 1
for name, n in sorted(modules.items()):
    if n > 80:
        print(f"WARNING: modules/{os.fsdecode(name)} has {n} files (target <= 80)")
if count >= 7000 or size >= 90 * 1048576:
    print("ERROR: repository budget exceeded")
    sys.exit(1)
print("Budget check passed.")
'
