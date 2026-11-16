<?php

declare(strict_types=1);

namespace Erp\Depth;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

/**
 * Depth gate from docs/MASTER-PLAN.md §7. Static heuristics over specs/<id>.md and module code;
 * module tests run only when requested and only after the static gate passes.
 */
final class Inspector
{
    public const WEIGHTS = ['entity_cov' => 20, 'event_cov' => 15, 'contract_cov' => 20, 'rule_cov' => 20, 'perm_cov' => 10, 'ui_cov' => 10, 'ai_cov' => 5];

    private const MARKERS = '/\b(TODO|FIXME|NotImplemented|not\s+implemented|placeholder|stub|lorem|dummy)\b/i';

    private const CODE_EXTENSIONS = ['php', 'js', 'jsx', 'ts', 'tsx', 'sql'];

    private const CRUD_ACTIONS = ['index', 'store', 'show', 'update', 'destroy', 'create', 'edit'];

    private const IDEMPOTENCY = '/idempoten|firstOrCreate|updateOrCreate|updateOrInsert|upsert|insertOrIgnore|lockForUpdate|->\s*(?:exists|doesntExist)\s*\(\s*\)|\$event\s*->\s*id\b/i';

    private const STOPWORDS = ['with', 'from', 'that', 'this', 'only', 'when', 'then', 'than', 'into', 'each', 'both', 'must', 'have', 'been', 'will', 'also', 'unless', 'allowed', 'required', 'should', 'their', 'there', 'which', 'where', 'after', 'before', 'other', 'more', 'less', 'same', 'such', 'under', 'over', 'within', 'without', 'between', 'rule', 'rules', 'test', 'true', 'false', 'null', 'optional', 'default', 'never', 'always', 'per', 'all', 'any', 'can', 'not', 'the', 'and', 'for', 'via'];

    /** @var list<string>|null */
    private ?array $moduleIds = null;

    /** @param list<string>|null $testCommand command prefix; the module test directory is appended */
    public function __construct(private readonly string $root, private readonly ?array $testCommand = null) {}

    /** @return list<array<string, mixed>> */
    public function inspectAll(bool $runTests = false): array
    {
        $ids = $this->moduleIds();
        if ($ids === []) {
            throw new RuntimeException('No module manifests found');
        }

        return array_map(fn (string $id) => $this->inspect($id, $runTests), $ids);
    }

    /** @return array<string, mixed> */
    public function inspect(string $id, bool $runTests = false): array
    {
        if (preg_match('/^[a-z][a-z0-9-]*$/D', $id) !== 1) {
            throw new RuntimeException('Invalid module id');
        }
        $directory = $this->root.'/modules/'.$id;
        if (! is_file($directory.'/module.json')) {
            throw new RuntimeException("Module $id not found");
        }
        $manifest = json_decode((string) file_get_contents($directory.'/module.json'), true);
        if (! is_array($manifest) || ($manifest['id'] ?? null) !== $id || ! in_array($manifest['status'] ?? null, ['stub', 'partial', 'done'], true)) {
            throw new RuntimeException("Module $id has an invalid manifest id or status");
        }
        $files = $this->files($directory);
        $code = $this->index($directory, $files);
        $specPath = $this->root.'/specs/'.$id.'.md';
        $spec = is_file($specPath) ? SpecParser::parse((string) file_get_contents($specPath), $id, $this->moduleIds()) : null;
        $categories = [];
        foreach (['entity_cov' => 'entityCoverage', 'event_cov' => 'eventCoverage', 'contract_cov' => 'contractCoverage', 'rule_cov' => 'ruleCoverage', 'perm_cov' => 'permissionCoverage', 'ui_cov' => 'uiCoverage', 'ai_cov' => 'aiCoverage'] as $category => $method) {
            if ($spec === null) {
                $categories[$category] = ['percent' => 0.0, 'covered' => 0, 'total' => 0, 'missing' => ['specification missing: specs/'.$id.'.md']];

                continue;
            }
            [$covered, $total, $missing] = $this->{$method}($id, $manifest, $spec, $code);
            $categories[$category] = ['percent' => $total === 0 ? 100.0 : round(100 * $covered / $total, 1), 'covered' => $covered, 'total' => $total, 'missing' => $missing];
        }
        $violations = $this->antiGaming($directory, $files, $spec, $code);
        $score = 0.0;
        foreach (self::WEIGHTS as $category => $weight) {
            $score += $categories[$category]['percent'] * $weight / 100;
        }
        $score = round($score, 1);
        $staticPass = $spec !== null && $score >= 80 && min(array_column($categories, 'percent')) >= 60 && $violations === [];
        $tests = ['status' => 'not_run', 'detail' => $staticPass ? 'run with --gate or --run-tests' : 'static gate not passed'];
        if ($runTests && $staticPass) {
            $tests = $this->runTests($id);
        }
        $pass = $staticPass && $tests['status'] === 'passed';
        // Contracts are declarations packaged for every module; they are not an implementation.
        $implemented = array_filter($files, fn (string $file) => preg_match('#^(src|Providers|routes|database|tests)/.+\.php$#', $file) === 1 && ! str_starts_with($file, 'src/Contracts/')) !== [];

        return [
            'id' => $id,
            'status' => $manifest['status'],
            'score' => $score,
            'categories' => $categories,
            'violations' => $violations,
            'tests' => $tests,
            'pass' => $pass,
            'suggested_status' => $pass ? 'done' : ($implemented ? 'partial' : 'stub'),
        ];
    }

    /** @return list<string> */
    public function moduleIds(): array
    {
        if ($this->moduleIds === null) {
            $paths = glob($this->root.'/modules/*/module.json') ?: [];
            $this->moduleIds = array_map(fn (string $path) => basename(dirname($path)), $paths);
            sort($this->moduleIds, SORT_STRING);
        }

        return $this->moduleIds;
    }

    /**
     * @param  array<string, mixed>  $manifest
     * @param  array<string, mixed>  $spec
     * @param  array<string, mixed>  $code
     * @return array{int, int, list<string>}
     */
    private function entityCoverage(string $id, array $manifest, array $spec, array $code): array
    {
        $prefix = str_replace('-', '_', $id).'__';
        [$covered, $total, $missing] = [0, 0, []];
        foreach ($spec['entities'] as $entity) {
            $total += 1 + count($entity['fields']);
            $table = $this->tableFor($entity, $prefix, $code['columns']);
            if ($table === null) {
                $missing[] = $entity['name'].': table missing (expected '.($entity['table'] ?? $prefix.self::plural(self::snake($entity['name']))).', '.count($entity['fields']).' fields)';

                continue;
            }
            $covered++;
            $absent = [];
            foreach ($entity['fields'] as $field) {
                if (in_array($field, $code['columns'][$table], true) || in_array($field.'_id', $code['columns'][$table], true)) {
                    $covered++;
                } else {
                    $absent[] = $field;
                }
            }
            if ($absent !== []) {
                $missing[] = $entity['name'].' ('.$table.'): missing columns '.implode(', ', $absent);
            }
        }

        return [$covered, $total, $missing];
    }

    /**
     * @param  array<string, mixed>  $manifest
     * @param  array<string, mixed>  $spec
     * @param  array<string, mixed>  $code
     * @return array{int, int, list<string>}
     */
    private function eventCoverage(string $id, array $manifest, array $spec, array $code): array
    {
        $source = implode("\n", $this->paths($code['backend'], 'src/'));
        $provider = $code['backend']['Providers/ModuleServiceProvider.php'] ?? '';
        [$covered, $missing] = [0, []];
        foreach ($spec['events_out'] as $event) {
            $emitted = preg_match('/(?:DomainEvent::create|new\s+\\\\?(?:[\w\\\\]+\\\\)?DomainEvent)\s*\(\s*[\'"]'.preg_quote($event, '/').'[\'"]/', $source) === 1;
            $declared = in_array($event, $manifest['emits'] ?? [], true);
            if ($emitted && $declared) {
                $covered++;
            } else {
                $missing[] = 'out '.$event.': '.implode(' and ', array_filter([$emitted ? null : 'not emitted via DomainEvent in src/', $declared ? null : 'not in manifest emits']));
            }
        }
        preg_match_all('/[\'"]event[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]handler[\'"]\s*=>\s*\\\\?([\w\\\\]+)::class/', $provider, $registrations, PREG_SET_ORDER);
        foreach ($spec['events_in'] as $pattern) {
            $reason = 'no listeners() registration in Providers/ModuleServiceProvider.php';
            $ok = false;
            foreach ($registrations as [, $event, $handlerClass]) {
                if (! fnmatch($pattern, $event)) {
                    continue;
                }
                $handler = $this->method($code, basename(str_replace('\\', '/', $handlerClass)), 'handle');
                if (! in_array($event, $manifest['listens'] ?? [], true)) {
                    $reason = $event.' not in manifest listens';
                } elseif ($handler === null || $handler['body'] === null) {
                    $reason = 'handler '.$handlerClass.'::handle not found in src/';
                } elseif (PhpAnalyzer::trivialReason($handler['body']) !== null) {
                    $reason = 'handler '.$handlerClass.'::handle is trivial';
                } elseif (preg_match(self::IDEMPOTENCY, implode(' ', $handler['body'])) !== 1) {
                    $reason = 'handler '.$handlerClass.'::handle shows no idempotency guard';
                } else {
                    $ok = true;
                }
            }
            $ok ? $covered++ : $missing[] = 'in '.$pattern.': '.$reason;
        }

        return [$covered, count($spec['events_out']) + count($spec['events_in']), $missing];
    }

    /**
     * @param  array<string, mixed>  $manifest
     * @param  array<string, mixed>  $spec
     * @param  array<string, mixed>  $code
     * @return array{int, int, list<string>}
     */
    private function contractCoverage(string $id, array $manifest, array $spec, array $code): array
    {
        $declared = [];
        $implementations = [];
        foreach ($code['backend'] as $file => $source) {
            foreach (PhpAnalyzer::classes($source) as $class) {
                if ($class['kind'] === 'interface' && str_starts_with($file, 'src/Contracts/')) {
                    foreach ($class['methods'] as $method) {
                        $declared[$method] = $class['name'];
                    }
                } elseif ($class['kind'] === 'class' && str_starts_with($file, 'src/') && ! str_starts_with($file, 'src/Contracts/')) {
                    foreach ($class['implements'] as $interface) {
                        $implementations[$interface][] = $class['name'];
                    }
                }
            }
        }
        $required = array_values(array_unique(array_merge($spec['contract'], array_keys($declared))));
        [$covered, $missing] = [0, []];
        foreach ($required as $method) {
            if (! isset($declared[$method])) {
                $missing[] = $method.': not declared in a src/Contracts interface';

                continue;
            }
            $implementation = null;
            foreach ($implementations[$declared[$method]] ?? [] as $class) {
                $implementation ??= $this->method($code, $class, $method);
            }
            if ($implementation === null || $implementation['body'] === null) {
                $missing[] = $method.': no implementation of '.$declared[$method];
            } elseif (($reason = PhpAnalyzer::trivialReason($implementation['body'])) !== null) {
                $missing[] = $method.': implementation '.$reason;
            } else {
                $covered++;
            }
        }

        return [$covered, count($required), $missing];
    }

    /**
     * @param  array<string, mixed>  $manifest
     * @param  array<string, mixed>  $spec
     * @param  array<string, mixed>  $code
     * @return array{int, int, list<string>}
     */
    private function ruleCoverage(string $id, array $manifest, array $spec, array $code): array
    {
        [$covered, $missing] = [0, []];
        foreach ($spec['rules'] as $index => $rule) {
            $keywords = self::keywords($rule);
            [$named, $assertions] = [0, 0];
            foreach ($code['test_methods'] as $test) {
                $name = strtolower($test['name']);
                if (! str_contains($name, 'rule')) {
                    continue;
                }
                $hits = count(array_filter($keywords, fn (string $keyword) => str_contains($name, $keyword)));
                if ($keywords === [] ? preg_match('/rule_?'.($index + 1).'(?!\d)/', $name) === 1 : $hits >= min(2, count($keywords))) {
                    $named++;
                    $assertions += $test['assertions'];
                }
            }
            if ($named > 0 && $assertions >= 2) {
                $covered++;
            } else {
                $hint = $keywords === [] ? 'test_rule_'.($index + 1).'_*' : 'test_rule_*'.implode('*', array_slice($keywords, 0, 2)).'*';
                $missing[] = 'rule '.($index + 1).' "'.self::excerpt($rule).'": '.($named === 0 ? 'no test named '.$hint : 'only '.$assertions.' assertion(s)');
            }
        }

        return [$covered, count($spec['rules']), $missing];
    }

    /**
     * @param  array<string, mixed>  $manifest
     * @param  array<string, mixed>  $spec
     * @param  array<string, mixed>  $code
     * @return array{int, int, list<string>}
     */
    private function permissionCoverage(string $id, array $manifest, array $spec, array $code): array
    {
        $required = array_values(array_unique(array_merge($manifest['permissions'] ?? [], $spec['permissions'])));
        sort($required, SORT_STRING);
        $routes = $code['backend']['routes/api.php'] ?? '';
        $source = implode("\n", $this->paths($code['backend'], 'src/'));
        $ui = implode("\n", array_merge($this->paths($code['web'], 'web/pages/'), $this->paths($code['web'], 'web/components/'), [$code['web']['web/routes.ts'] ?? '']));
        [$covered, $missing] = [0, []];
        foreach ($required as $permission) {
            $quoted = preg_quote($permission, '/');
            if (! in_array($permission, $manifest['permissions'] ?? [], true)) {
                $missing[] = $permission.': in spec but not declared in manifest permissions';

                continue;
            }
            $api = preg_match('/[\'"]can:'.$quoted.'(?:,[^\'"]*)?[\'"]|->can\(\s*[\'"]'.$quoted.'[\'"]/', $routes) === 1
                || preg_match('/\b(?:authorize|allows|denies|can|cannot|check)\w*\s*\([^;]*[\'"]'.$quoted.'[\'"]/', $source) === 1;
            $inUi = preg_match('/[\'"]'.$quoted.'[\'"]/', $ui) === 1;
            $covered += (int) $api + (int) $inUi;
            if (! $api || ! $inUi) {
                $missing[] = $permission.': '.implode(' and ', array_filter([$api ? null : 'not enforced by an API route or authorization check', $inUi ? null : 'not used by a UI route or page']));
            }
        }

        return [$covered, 2 * count($required), $missing];
    }

    /**
     * @param  array<string, mixed>  $manifest
     * @param  array<string, mixed>  $spec
     * @param  array<string, mixed>  $code
     * @return array{int, int, list<string>}
     */
    private function uiCoverage(string $id, array $manifest, array $spec, array $code): array
    {
        $entities = array_values(array_filter($manifest['entities'] ?? [], 'is_string'));
        if ($entities === []) {
            $entities = array_map(fn (array $entity) => self::snake($entity['name']), $spec['entities']);
        }
        $routes = $code['web']['web/routes.ts'] ?? '';
        preg_match_all('/import\s+(\w+)\s+from\s+[\'"]\.\/pages\/([\w\/]+)[\'"]/', $routes, $imports, PREG_SET_ORDER);
        $components = [];
        foreach ($imports as [, $component, $path]) {
            $components[basename($path)] = $component;
        }
        preg_match_all('/\{[^{}]*\}/', $routes, $entries);
        $routed = [];
        foreach ($entries[0] as $entry) {
            if (preg_match('/\bcomponent\s*:\s*(\w+)/', $entry, $component) === 1 && preg_match('/\bpath\s*:/', $entry) === 1 && preg_match('/\bpermission\s*:\s*[\'"][^\'"]+[\'"]/', $entry) === 1) {
                $routed[$component[1]] = true;
            }
        }
        $translations = json_decode($code['web']['web/i18n/fa.json'] ?? '', true);
        $keys = is_array($translations) ? array_keys(self::flatten($translations)) : [];
        [$covered, $total, $missing] = [0, 0, []];
        foreach ($entities as $entity) {
            $studly = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $entity)));
            foreach (['List', 'Form', 'Detail'] as $kind) {
                $page = $studly.$kind.'Page';
                $exists = isset($code['web']['web/pages/'.$page.'.tsx']);
                $total++;
                if ($exists && isset($components[$page], $routed[$components[$page]])) {
                    $covered++;
                } else {
                    $missing[] = $entity.': '.strtolower($kind).' page web/pages/'.$page.'.tsx '.($exists ? 'not routed with path and permission' : 'missing');
                }
            }
            $total++;
            if (array_filter($keys, fn (string $key) => str_starts_with($key, $entity.'.')) !== []) {
                $covered++;
            } else {
                $missing[] = $entity.': no web/i18n/fa.json keys under "'.$entity.'."';
            }
        }
        preg_match_all('/\bt\(\s*[\'"]([^\'"]+)[\'"]/', implode("\n", $this->paths($code['web'], 'web/pages/')), $used);
        foreach (array_unique($used[1]) as $key) {
            $total++;
            in_array($key, $keys, true) ? $covered++ : $missing[] = 'fa.json key missing: '.$key;
        }

        return [$covered, $total, $missing];
    }

    /**
     * @param  array<string, mixed>  $manifest
     * @param  array<string, mixed>  $spec
     * @param  array<string, mixed>  $code
     * @return array{int, int, list<string>}
     */
    private function aiCoverage(string $id, array $manifest, array $spec, array $code): array
    {
        $declared = [];
        foreach ($manifest['ai']['capabilities'] ?? [] as $capability) {
            if (is_array($capability) && is_string($capability['id'] ?? null)) {
                $declared[$capability['id']] = $capability;
            }
        }
        $provider = $code['backend']['Providers/ModuleServiceProvider.php'] ?? '';
        [$covered, $total, $missing] = [0, 0, []];
        foreach (array_values(array_unique(array_merge(array_keys($declared), $spec['ai']))) as $capability) {
            $tools = array_values(array_filter($declared[$capability]['tools'] ?? [], 'is_string'));
            $unregistered = array_filter($tools, fn (string $tool) => ! str_contains($provider, "'".$tool."'") && ! str_contains($provider, '"'.$tool.'"'));
            $total += 3;
            if (! isset($declared[$capability])) {
                $missing[] = $capability.': in spec but not declared in manifest ai.capabilities';
            } elseif ($tools === [] || $unregistered !== []) {
                $missing[] = $capability.': '.($tools === [] ? 'no tools declared' : 'tools not registered in aiTools(): '.implode(', ', $unregistered));
            } else {
                $covered++;
            }
            foreach (['off', 'fake'] as $mode) {
                $tested = array_filter($code['test_methods'], fn (array $test) => str_contains(strtolower($test['name']), $capability) && str_contains(strtolower($test['name']), $mode) && $test['assertions'] > 0);
                $tested !== [] ? $covered++ : $missing[] = $capability.': no test named *'.$capability.'*'.$mode.'* with assertions';
            }
        }

        return [$covered, $total, $missing];
    }

    /**
     * @param  list<string>  $files
     * @param  array<string, mixed>|null  $spec
     * @param  array<string, mixed>  $code
     * @return list<string>
     */
    private function antiGaming(string $directory, array $files, ?array $spec, array $code): array
    {
        $violations = [];
        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (! in_array($extension, self::CODE_EXTENSIONS, true)) {
                continue;
            }
            $content = (string) file_get_contents($directory.'/'.$file);
            // An HTML/JSX input placeholder attribute is UI copy, not unfinished work.
            $scanned = in_array($extension, ['ts', 'tsx', 'js', 'jsx'], true) ? (string) preg_replace('/\bplaceholder\s*[=:]/i', 'hint=', $content) : $content;
            foreach (preg_split('/\R/', $scanned) ?: [] as $index => $line) {
                if (preg_match_all(self::MARKERS, $line, $matches) > 0) {
                    foreach (array_unique($matches[1]) as $marker) {
                        $violations[] = $file.':'.($index + 1).": banned marker '".$marker."'";
                    }
                }
            }
            if ($extension === 'php' && ! str_starts_with($file, 'tests/')) {
                foreach (PhpAnalyzer::trivialFunctions($content) as $function) {
                    if (! (str_starts_with($file, 'database/migrations/') && $function['name'] === 'down')) {
                        $violations[] = $file.':'.$function['line'].': '.$function['name'].' '.$function['reason'];
                    }
                }
            }
            $backendPath = preg_match('#^(web|tests|database/factories)/#', $file) !== 1;
            if ($backendPath && (preg_match('#(^|/)(mocks?|fakes?|fixtures|dummy)/#i', $file) === 1 || preg_match('/(Mock|Fake|Dummy)\w*\.\w+$/', basename($file)) === 1
                || preg_match('/\bFaker\\\\|\bfake\(\)|\bmock(?:Data|Rows|Items|Records)\b/', $content) === 1)) {
                $violations[] = $file.': mock or fake data in a backend path';
            }
        }
        foreach ($code['test_methods'] as $test) {
            if ($test['skipped']) {
                $violations[] = $test['file'].':'.$test['line'].': '.$test['name'].' is skipped or incomplete';
            } elseif ($test['assertions'] === 0) {
                $violations[] = $test['file'].':'.$test['line'].': '.$test['name'].' asserts nothing';
            }
        }
        [$crud, $routes] = $this->routeShape($code['backend']['routes/api.php'] ?? '');
        if ($spec !== null && $spec['rules'] !== [] && $routes > 0 && $crud / $routes >= 0.8) {
            $violations[] = 'routes/api.php: '.$crud.' of '.$routes.' routes are generic CRUD although the spec has business rules';
        }
        sort($violations, SORT_STRING);

        return $violations;
    }

    /** @return array{int, int} generic CRUD routes, all routes */
    private function routeShape(string $routes): array
    {
        [$crud, $total] = [0, 0];
        preg_match_all('/Route::(apiResource|resource|get|post|put|patch|delete|any|match)\s*\(([^;]*)/', $routes, $matches, PREG_SET_ORDER);
        foreach ($matches as [, $verb, $arguments]) {
            if ($verb === 'apiResource' || $verb === 'resource') {
                $count = $verb === 'apiResource' ? 5 : 7;
                [$crud, $total] = [$crud + $count, $total + $count];

                continue;
            }
            $total++;
            if (preg_match('/::class\s*,\s*[\'"]('.implode('|', self::CRUD_ACTIONS).')[\'"]|@('.implode('|', self::CRUD_ACTIONS).')[\'"]/', $arguments) === 1) {
                $crud++;
            }
        }

        return [$crud, $total];
    }

    /** @return array{status:string, detail:string} */
    private function runTests(string $id): array
    {
        $relative = 'modules/'.$id.'/tests';
        if (! is_dir($this->root.'/'.$relative)) {
            return ['status' => 'failed', 'detail' => 'no tests directory'];
        }
        $command = [...($this->testCommand ?? [PHP_BINARY, $this->root.'/vendor/bin/phpunit']), $relative];
        // @phpstan-ignore argument.type (PHP 7.4+ documents the ['redirect', 1] descriptor)
        $process = proc_open($command, [1 => ['pipe', 'w'], 2 => ['redirect', 1]], $pipes, $this->root);
        if (! is_resource($process)) {
            return ['status' => 'failed', 'detail' => 'cannot start test command'];
        }
        $output = (string) stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $exit = proc_close($process);
        if ($exit === 0 && preg_match('/^OK \((\d+) tests?, (\d+) assertions?\)/m', $output, $match) === 1 && (int) $match[1] > 0) {
            return ['status' => 'passed', 'detail' => $match[0]];
        }
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/', $output) ?: []), fn (string $line) => $line !== ''));

        return ['status' => 'failed', 'detail' => 'exit '.$exit.': '.mb_substr(implode(' | ', array_slice($lines, -3)), 0, 300)];
    }

    /**
     * @param  list<string>  $files
     * @return array{backend: array<string, string>, columns: array<string, list<string>>, functions: list<array{file:string, name:string, line:int, class:?string, prefix:string, body:?list<string>}>, test_methods: list<array{file:string, name:string, line:int, assertions:int, skipped:bool}>, web: array<string, string>}
     */
    private function index(string $directory, array $files): array
    {
        $index = ['backend' => [], 'columns' => [], 'functions' => [], 'test_methods' => [], 'web' => []];
        foreach ($files as $file) {
            $isPhp = str_ends_with($file, '.php');
            if ($isPhp && preg_match('#^(src|Providers|routes|database)/#', $file) === 1) {
                $source = (string) file_get_contents($directory.'/'.$file);
                $index['backend'][$file] = $source;
                foreach (PhpAnalyzer::functions($source) as $function) {
                    $index['functions'][] = ['file' => $file] + $function;
                }
                if (str_starts_with($file, 'database/migrations/')) {
                    foreach (PhpAnalyzer::migrationColumns($source) as $table => $columns) {
                        $index['columns'][$table] = array_values(array_unique(array_merge($index['columns'][$table] ?? [], $columns)));
                    }
                }
            } elseif ($isPhp && str_starts_with($file, 'tests/')) {
                foreach (PhpAnalyzer::functions((string) file_get_contents($directory.'/'.$file)) as $function) {
                    if ($function['body'] !== null && PhpAnalyzer::isTest($function)) {
                        $index['test_methods'][] = ['file' => $file, 'name' => $function['name'], 'line' => $function['line'], 'assertions' => PhpAnalyzer::assertions($function['body']), 'skipped' => PhpAnalyzer::skipped($function['body'])];
                    }
                }
            } elseif (str_starts_with($file, 'web/') && preg_match('/\.(tsx?|jsx?|json)$/', $file) === 1) {
                $index['web'][$file] = (string) file_get_contents($directory.'/'.$file);
            }
        }

        return $index;
    }

    /**
     * @param  array<string, mixed>  $code
     * @return array{file:string, name:string, line:int, class:?string, prefix:string, body:?list<string>}|null
     */
    private function method(array $code, string $class, string $name): ?array
    {
        foreach ($code['functions'] as $function) {
            if ($function['class'] === $class && $function['name'] === $name && str_starts_with($function['file'], 'src/')) {
                return $function;
            }
        }

        return null;
    }

    /**
     * @param  array{name:string, table:?string, fields:list<string>}  $entity
     * @param  array<string, list<string>>  $columns
     */
    private function tableFor(array $entity, string $prefix, array $columns): ?string
    {
        if ($entity['table'] !== null) {
            return isset($columns[$entity['table']]) ? $entity['table'] : null;
        }
        $words = explode('_', self::snake($entity['name']));
        for ($start = 0; $start < count($words); $start++) {
            $name = implode('_', array_slice($words, $start));
            foreach ([$prefix.self::plural($name), $prefix.$name] as $candidate) {
                if (isset($columns[$candidate])) {
                    return $candidate;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, string>  $files
     * @return array<string, string>
     */
    private function paths(array $files, string $prefix): array
    {
        return array_filter($files, fn (string $file) => str_starts_with($file, $prefix), ARRAY_FILTER_USE_KEY);
    }

    /** @return list<string> paths relative to the module directory; symlinks are rejected */
    private function files(string $directory): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $file) {
            $relative = substr($file->getPathname(), strlen($directory) + 1);
            if ($file->isLink()) {
                throw new RuntimeException('Symlink in module directory: '.basename($directory).'/'.$relative);
            }
            if ($file->isFile() && ! str_contains('/'.$relative, '/node_modules/')) {
                $files[] = $relative;
            }
        }
        sort($files, SORT_STRING);

        return $files;
    }

    /** @return list<string> */
    private static function keywords(string $rule): array
    {
        preg_match_all('/[a-z]{4,}/', strtolower((string) preg_replace('/([a-z])([A-Z])/', '$1 $2', $rule)), $words);

        return array_values(array_unique(array_diff($words[0], self::STOPWORDS)));
    }

    private static function excerpt(string $text): string
    {
        $text = (string) preg_replace('/\s+/u', ' ', $text);

        return mb_strlen($text) > 70 ? mb_substr($text, 0, 67).'...' : $text;
    }

    private static function snake(string $name): string
    {
        return strtolower((string) preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/', '/[\s-]+/'], ['$1_$2', '$1_$2', '_'], $name));
    }

    private static function plural(string $name): string
    {
        return match (true) {
            preg_match('/[^aeiou]y$/', $name) === 1 => substr($name, 0, -1).'ies',
            preg_match('/(s|x|z|ch|sh)$/', $name) === 1 => $name.'es',
            default => $name.'s',
        };
    }

    /**
     * @param  array<mixed>  $data
     * @return array<string, mixed>
     */
    private static function flatten(array $data, string $prefix = ''): array
    {
        $flat = [];
        foreach ($data as $key => $value) {
            $name = $prefix.$key;
            $flat[$name] = $value;
            if (is_array($value)) {
                $flat += self::flatten($value, $name.'.');
            }
        }

        return $flat;
    }
}
