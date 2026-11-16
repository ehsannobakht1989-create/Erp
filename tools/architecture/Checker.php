<?php

declare(strict_types=1);

namespace Erp\Architecture;

use FilesystemIterator;
use PhpToken;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/** Static architecture rules for Laravel module code (legacy modules/<id>/backend is excluded until removal). */
final class Checker
{
    private const TABLE_METHODS = ['table', 'create', 'drop', 'dropIfExists', 'rename', 'from', 'join', 'leftJoin', 'rightJoin', 'crossJoin', 'joinSub', 'hasTable', 'hasColumn', 'hasColumns', 'getColumnListing'];

    private const RAW_METHODS = ['raw', 'whereRaw', 'orWhereRaw', 'havingRaw', 'orHavingRaw', 'orderByRaw', 'selectRaw', 'groupByRaw', 'fromRaw', 'statement', 'unprepared'];

    private const DB_QUERY_METHODS = ['select', 'selectOne', 'scalar', 'cursor', 'insert', 'update', 'delete', 'affectingStatement'];

    private const WRITE_METHODS = ['insert', 'insertGetId', 'insertOrIgnore', 'insertUsing', 'upsert', 'update', 'updateOrInsert', 'delete', 'forceDelete', 'increment', 'decrement', 'incrementEach', 'decrementEach', 'truncate'];

    private const BYPASS_METHODS = ['saveQuietly', 'updateQuietly', 'deleteQuietly', 'forceDeleteQuietly', 'restoreQuietly', 'withoutEvents', 'unsetEventDispatcher', 'flushEventListeners'];

    private const MARKERS = '/\b(TODO|FIXME|NotImplemented|not implemented|placeholder|stub|lorem|dummy)\b/i';

    public function __construct(private readonly string $root) {}

    /** @return list<string> */
    public function run(): array
    {
        $violations = [];
        foreach (glob($this->root.'/modules/*', GLOB_ONLYDIR) ?: [] as $directory) {
            $id = basename($directory);
            foreach ($this->phpFiles($directory, ['backend', 'web', 'node_modules']) as $file) {
                $violations = array_merge($violations, $this->checkModuleFile($id, $file));
            }
        }
        foreach ($this->phpFiles($this->root.'/app', []) as $file) {
            $violations = array_merge($violations, $this->checkRawSql($this->relative($file), PhpToken::tokenize((string) file_get_contents($file)), true));
        }
        sort($violations, SORT_STRING);

        return $violations;
    }

    /** @return list<string> */
    private function checkModuleFile(string $id, string $file): array
    {
        $relative = $this->relative($file);
        $content = (string) file_get_contents($file);
        $tokens = array_values(array_filter(PhpToken::tokenize($content), fn (PhpToken $t) => ! $t->is([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])));
        $inModule = substr($relative, strlen('modules/'.$id.'/'));
        $violations = $this->checkRawSql($relative, PhpToken::tokenize($content), false);
        $own = str_replace(' ', '', ucwords(str_replace('-', ' ', $id)));
        $prefix = str_replace('-', '_', $id).'__';
        if (preg_match(self::MARKERS, $content, $marker) === 1) {
            $violations[] = "$relative: banned marker '{$marker[1]}'";
        }
        foreach ($tokens as $i => $token) {
            if ($token->is([T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED])) {
                $parts = explode('\\', ltrim($token->text, '\\'));
                if ($parts[0] === 'Modules' && ($parts[1] ?? '') !== $own && ($parts[2] ?? '') !== 'Contracts') {
                    $violations[] = "$relative:{$token->line}: imports another module's non-contract class ".ltrim($token->text, '\\');
                }
            }
            if ($token->is(T_STRING) && in_array($token->text, self::BYPASS_METHODS, true) && $this->isCall($tokens, $i)) {
                $violations[] = "$relative:{$token->line}: {$token->text} bypasses audit and events";
            }
            if ($token->is(T_STRING) && in_array($token->text, self::TABLE_METHODS, true) && $this->isCall($tokens, $i) && ($tokens[$i + 2] ?? null)?->is(T_CONSTANT_ENCAPSED_STRING)) {
                $table = $this->literal($tokens[$i + 2]->text);
                if (preg_match('/^([a-z][a-z0-9_]*)(\s+as\s+\w+)?$/i', $table, $m) === 1 && ! str_starts_with($m[1], $prefix)) {
                    $violations[] = "$relative:{$token->line}: table {$m[1]} is outside prefix $prefix";
                }
            }
            if ($token->is(T_VARIABLE) && $token->text === '$table' && ($tokens[$i + 1] ?? null)?->text === '=' && ($tokens[$i + 2] ?? null)?->is(T_CONSTANT_ENCAPSED_STRING) && ! str_starts_with($this->literal($tokens[$i + 2]->text), $prefix)) {
                $violations[] = "$relative:{$token->line}: model table ".$this->literal($tokens[$i + 2]->text)." is outside prefix $prefix";
            }
            if ($token->is(T_CONSTANT_ENCAPSED_STRING) && preg_match('/^\s*(select|insert|update|delete|with)\b/i', $this->literal($token->text)) === 1) {
                preg_match_all('/\b(?:from|join|into|update)\s+`?([a-z][a-z0-9_]*)`?/i', $this->literal($token->text), $tables);
                foreach ($tables[1] as $table) {
                    if (! str_starts_with($table, $prefix)) {
                        $violations[] = "$relative:{$token->line}: SQL references table $table outside prefix $prefix";
                    }
                }
            }
            if ($token->is(T_STRING) && $token->text === 'Route' && ($tokens[$i + 1] ?? null)?->is(T_DOUBLE_COLON) && $inModule !== 'routes/api.php') {
                $violations[] = "$relative:{$token->line}: module routes must be declared in routes/api.php";
            }
        }
        if (preg_match('/\bclass\s+\w+\s+extends\s+\\\\?(?:[\w\\\\]*\\\\)?(?:Model|Authenticatable|Pivot)\b/', $content, $class, PREG_OFFSET_CAPTURE) === 1
            && preg_match('/\buse\s+\\\\?(?:[\w\\\\]*\\\\)?Auditable\b/', substr($content, $class[0][1])) !== 1) {
            $violations[] = "$relative: Eloquent model must use App\\Support\\Audit\\Auditable";
        }
        foreach ($this->statements($tokens) as $statement) {
            $texts = array_map(fn (PhpToken $t) => $t->text, $statement);
            $joined = implode(' ', $texts);
            $write = false;
            foreach ($statement as $j => $token) {
                $write = $write || ($token->is(T_STRING) && in_array($token->text, self::WRITE_METHODS, true) && ($statement[$j - 1] ?? null)?->is([T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR]) && ($statement[$j + 1] ?? null)?->text === '(');
            }
            $builder = preg_match('/\bDB :: table \(|-> table \(|:: query \(|:: where\w* \(|:: whereIn \(/', $joined) === 1;
            if ($write && $builder && ! str_starts_with($inModule, 'database/') && ! str_contains($content, 'AuditContract')) {
                $violations[] = "$relative:{$statement[0]->line}: query-builder/bulk write without explicit AuditContract recording";
            }
            if ($inModule === 'routes/api.php' && preg_match('/\bRoute :: (get|post|put|patch|delete|options|any|match|resource|apiResource|resources|apiResources|group|middleware|prefix|name|controller)\b/', $joined) === 1
                && preg_match('/[\'"]can:|-> can \(/', $joined) !== 1) {
                $violations[] = "$relative:{$statement[0]->line}: route statement lacks a can: permission";
            }
        }

        return $violations;
    }

    /**
     * @param  list<PhpToken>  $tokens
     * @return list<string>
     */
    private function checkRawSql(string $relative, array $tokens, bool $allowAnnotation): array
    {
        $violations = [];
        $lines = [];
        foreach ($tokens as $token) {
            if ($token->is(T_COMMENT) && str_contains($token->text, 'arch-allow-raw:')) {
                $lines[$token->line + 1] = true;
            }
        }
        $code = array_values(array_filter($tokens, fn (PhpToken $t) => ! $t->is([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])));
        foreach ($code as $i => $token) {
            if (! $token->is(T_STRING) || ! $this->isCall($code, $i)) {
                continue;
            }
            $static = ($code[$i - 1] ?? null)?->is(T_DOUBLE_COLON) && preg_match('/(^|\\\\)DB$/', $code[$i - 2]->text ?? '') === 1;
            if (! in_array($token->text, self::RAW_METHODS, true) && ! ($static && in_array($token->text, self::DB_QUERY_METHODS, true))) {
                continue;
            }
            $depth = 0;
            for ($j = $i + 2; isset($code[$j]); $j++) {
                $text = $code[$j]->text;
                if (in_array($text, ['(', '[', '{'], true) || $code[$j]->is([T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES])) {
                    $depth++;
                } elseif (in_array($text, [')', ']', '}'], true)) {
                    if ($depth-- === 0) {
                        break;
                    }
                } elseif ($depth === 0 && $text === ',') {
                    break;
                }
                if ($code[$j]->is([T_VARIABLE, T_ENCAPSED_AND_WHITESPACE]) || $text === '.' || $text === '"' || ($code[$j]->is(T_STRING) && ($code[$j + 1] ?? null)?->text === '(')) {
                    if (! ($allowAnnotation && isset($lines[$token->line]))) {
                        $violations[] = "$relative:{$token->line}: string-built SQL passed to {$token->text}()";
                    }
                    break;
                }
            }
        }

        return $violations;
    }

    /** @param list<PhpToken> $tokens */
    private function isCall(array $tokens, int $i): bool
    {
        return ($tokens[$i - 1] ?? null)?->is([T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON]) === true && ($tokens[$i + 1] ?? null)?->text === '(';
    }

    /**
     * @param  list<PhpToken>  $tokens
     * @return list<list<PhpToken>>
     */
    private function statements(array $tokens): array
    {
        $statements = [];
        $current = [];
        $depth = 0;
        foreach ($tokens as $token) {
            $current[] = $token;
            if (in_array($token->text, ['(', '['], true)) {
                $depth++;
            } elseif (in_array($token->text, [')', ']'], true)) {
                $depth--;
            } elseif ($depth === 0 && in_array($token->text, [';', '{', '}'], true)) {
                $statements[] = $current;
                $current = [];
            }
        }

        return $current === [] ? $statements : [...$statements, $current];
    }

    private function literal(string $text): string
    {
        return stripcslashes(substr($text, 1, -1));
    }

    private function relative(string $path): string
    {
        return substr($path, strlen($this->root) + 1);
    }

    /**
     * @param  list<string>  $skip
     * @return list<string>
     */
    private function phpFiles(string $directory, array $skip): array
    {
        if (! is_dir($directory)) {
            return [];
        }
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            $relative = substr($file->getPathname(), strlen($directory) + 1);
            if ($file->isFile() && $file->getExtension() === 'php' && ! in_array(explode('/', $relative)[0], $skip, true)) {
                $files[] = $file->getPathname();
            }
        }
        sort($files, SORT_STRING);

        return $files;
    }
}
