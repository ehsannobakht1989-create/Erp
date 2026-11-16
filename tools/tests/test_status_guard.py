import importlib.util
import json
import tempfile
import unittest
from pathlib import Path

path = Path(__file__).resolve().parents[1] / 'status_guard.py'
spec = importlib.util.spec_from_file_location('status_guard', path)
guard = importlib.util.module_from_spec(spec)
spec.loader.exec_module(guard)


class StatusGuardTest(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory()
        self.addCleanup(self.temp.cleanup)
        self.root = Path(self.temp.name)

    def manifest(self, name, data):
        path = self.root / 'modules' / name / 'module.json'
        path.parent.mkdir(parents=True)
        path.write_text(data, encoding='utf-8')

    def test_partial_and_stub_are_accepted(self):
        self.manifest('a', json.dumps({'status': 'partial'}))
        self.manifest('b', json.dumps({'status': 'stub'}))
        self.assertEqual([], guard.check(self.root))

    def test_done_is_rejected(self):
        self.manifest('a', json.dumps({'status': 'done'}))
        self.assertIn('done is forbidden', guard.check(self.root)[0])

    def test_missing_invalid_and_non_object_fail_closed(self):
        self.manifest('a', '{}')
        self.manifest('b', '{')
        self.manifest('c', '[]')
        self.manifest('d', '{"status":"in_progress"}')
        self.assertEqual(4, len(guard.check(self.root)))

    def test_no_modules_is_rejected(self):
        self.assertEqual(['No module manifests found'], guard.check(self.root))


if __name__ == '__main__':
    unittest.main()
