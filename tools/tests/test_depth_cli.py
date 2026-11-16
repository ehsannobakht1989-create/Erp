import json
import os
from pathlib import Path
import shutil
import subprocess
import tempfile
import unittest

SOURCE = Path(__file__).resolve().parents[2]
PHP = os.environ.get('PHP_BINARY', 'php')


class DepthCliTest(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory()
        self.addCleanup(self.temp.cleanup)
        self.root = Path(self.temp.name)
        shutil.copytree(SOURCE / 'tools' / 'depth', self.root / 'tools' / 'depth')
        shutil.copy(SOURCE / 'tools' / 'depth-check.php', self.root / 'tools')
        self.module = self.root / 'modules' / 'example'
        self.module.mkdir(parents=True)
        self.write_status('partial')

    def write_status(self, status):
        (self.module / 'module.json').write_text(json.dumps({'id': 'example', 'status': status}))

    def run_cli(self, *args):
        return subprocess.run([PHP, str(self.root / 'tools' / 'depth-check.php'), *args], capture_output=True, text=True)

    def test_json_is_unverified(self):
        result = self.run_cli('--all', '--json', '--gate')
        self.assertEqual(0, result.returncode, result.stderr)
        report = json.loads(result.stdout)
        self.assertFalse(report['implementation_complete'])
        self.assertIsNone(report['modules'][0]['score'])
        self.assertFalse(report['modules'][0]['pass'])

    def test_done_fails_closed(self):
        self.write_status('done')
        self.assertEqual(1, self.run_cli('example', '--gate').returncode)
        self.assertEqual(1, self.run_cli('--all', '--gate').returncode)

    def test_invalid_input_fails(self):
        for args in ((), ('--gate',), ('../example',), ('--all', '--all'), ('missing',)):
            with self.subTest(args=args):
                self.assertEqual(2, self.run_cli(*args).returncode)

    def test_symlink_does_not_bypass_scan(self):
        (self.module / 'link.php').symlink_to('/nonexistent')
        self.assertEqual(2, self.run_cli('--all', '--gate').returncode)


if __name__ == '__main__':
    unittest.main()
