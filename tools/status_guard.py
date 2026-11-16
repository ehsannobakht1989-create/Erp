#!/usr/bin/env python3
"""Fail closed on completion claims until the independent depth gate exists.

This is a temporary Phase 1 safeguard, not a depth checker or a score.
"""
import json
import sys
from pathlib import Path


def check(root: Path) -> list[str]:
    errors = []
    manifests = sorted((root / 'modules').glob('*/module.json'))
    if not manifests:
        return ['No module manifests found']
    for path in manifests:
        try:
            data = json.loads(path.read_text(encoding='utf-8'))
        except (OSError, UnicodeError, json.JSONDecodeError) as exc:
            errors.append(f'{path.parent.name}: cannot read manifest: {exc}')
            continue
        if not isinstance(data, dict):
            errors.append(f'{path.parent.name}: manifest must be an object')
            continue
        status = data.get('status')
        if status == 'done':
            errors.append(f'{path.parent.name}: done is forbidden until the depth gate is implemented and run')
        elif status not in ('stub', 'partial'):
            errors.append(f'{path.parent.name}: invalid or missing status {status!r}')
    return errors


def main() -> int:
    errors = check(Path(__file__).resolve().parent.parent)
    if errors:
        for error in errors:
            print(f'ERROR: {error}', file=sys.stderr)
        return 1
    print('Completion-claim guard passed; no modules are marked done. This is not a depth gate.')
    return 0


if __name__ == '__main__':
    sys.exit(main())
