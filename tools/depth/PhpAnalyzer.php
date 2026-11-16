<?php

declare(strict_types=1);

namespace Erp\Depth;

use PhpToken;

/** Token-level PHP facts for the depth gate; heuristics, never a proof of behaviour. */
final class PhpAnalyzer
{
    private const COLUMN_TYPES = ['bigIncrements', 'bigInteger', 'binary', 'boolean', 'char', 'date', 'dateTime', 'dateTimeTz', 'decimal', 'double', 'enum', 'float', 'foreignId', 'foreignUlid', 'foreignUuid', 'geography', 'geometry', 'increments', 'integer', 'ipAddress', 'json', 'jsonb', 'longText', 'macAddress', 'mediumIncrements', 'mediumInteger', 'mediumText', 'set', 'smallIncrements', 'smallInteger', 'string', 'text', 'time', 'timeTz', 'timestamp', 'timestampTz', 'tinyIncrements', 'tinyInteger', 'tinyText', 'unsignedBigInteger', 'unsignedDecimal', 'unsignedInteger', 'unsignedMediumInteger', 'unsignedSmallInteger', 'unsignedTinyInteger', 'ulid', 'uuid', 'year'];

    /**
     * Named functions with their class, test markers and body token texts (null for declarations).
     *
     * @return list<array{name:string, line:int, class:?string, prefix:string, body:?list<string>}>
     */
    public static function functions(string $source): array
    {
        $tokens = self::tokens($source, true);
        $results = [];
        $class = null;
        $classDepth = null;
        $depth = 0;
        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            if ($token->text === '{' || $token->is([T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES])) {
                $depth++;

                continue;
            }
            if ($token->text === '}') {
                $depth--;
                if ($classDepth !== null && $depth < $classDepth) {
                    [$class, $classDepth] = [null, null];
                }

                continue;
            }
            if ($token->is([T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM]) && ! ($tokens[$i - 1] ?? null)?->is([T_DOUBLE_COLON, T_NEW]) && ($tokens[$i + 1] ?? null)?->is(T_STRING)) {
                $class = $tokens[$i + 1]->text;
                $classDepth = $depth + 1;

                continue;
            }
            if (! $token->is(T_FUNCTION)) {
                continue;
            }
            $nameToken = $tokens[$i + 1] ?? null;
            if ($nameToken !== null && $nameToken->text === '&') {
                $nameToken = $tokens[$i + 2] ?? null;
            }
            if ($nameToken === null || ! $nameToken->is(T_STRING)) {
                continue; // Closure: its braces are counted by the main loop.
            }
            $prefix = '';
            for ($k = $i - 1; $k >= 0 && ! in_array($tokens[$k]->text, [';', '{', '}'], true); $k--) {
                $prefix = $tokens[$k]->text.' '.$prefix;
            }
            $start = null;
            $parens = 0;
            for ($j = $i + 1; $j < $count; $j++) {
                $text = $tokens[$j]->text;
                if ($text === '(') {
                    $parens++;
                } elseif ($text === ')') {
                    $parens--;
                } elseif ($parens === 0 && $text === ';') {
                    break;
                } elseif ($parens === 0 && $text === '{') {
                    $start = $j;
                    break;
                }
            }
            $body = null;
            if ($start !== null) {
                $body = [];
                $level = 1;
                for ($j = $start + 1; $j < $count; $j++) {
                    $text = $tokens[$j]->text;
                    if ($text === '{' || $tokens[$j]->is([T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES])) {
                        $level++;
                    } elseif ($text === '}' && --$level === 0) {
                        break;
                    }
                    if (! $tokens[$j]->is(T_DOC_COMMENT)) {
                        $body[] = $text;
                    }
                }
                if ($level !== 0) {
                    continue; // Malformed source; php -l reports it separately.
                }
                $i = $j;
            } else {
                $i = $j;
            }
            $results[] = ['name' => $nameToken->text, 'line' => $token->line, 'class' => $class, 'prefix' => $prefix, 'body' => $body];
        }

        return $results;
    }

    /** @return list<array{line:int, name:string, reason:string}> */
    public static function trivialFunctions(string $source): array
    {
        $results = [];
        foreach (self::functions($source) as $function) {
            if ($function['body'] === null || strtolower($function['name']) === '__construct') {
                continue; // Promoted-property constructors legitimately have empty bodies.
            }
            $reason = self::trivialReason($function['body']);
            if ($reason !== null) {
                $results[] = ['line' => $function['line'], 'name' => $function['name'], 'reason' => $reason];
            }
        }

        return $results;
    }

    /** @param list<string> $body */
    public static function trivialReason(array $body): ?string
    {
        if ($body === []) {
            return 'empty body';
        }
        if (in_array(strtolower(implode('', $body)), ['returnnull;', 'return[];', 'returnarray();', 'return0;'], true)) {
            return 'only returns a trivial value';
        }
        if (strtolower($body[0]) === 'throw' && end($body) === ';' && count(array_keys($body, ';', true)) === 1 && ! in_array('{', $body, true)) {
            return 'only throws';
        }

        return null;
    }

    /** @param array{name:string, prefix:string} $function */
    public static function isTest(array $function): bool
    {
        return str_starts_with(strtolower($function['name']), 'test') || preg_match('/#\[\s*\\\\?(?:[\w\\\\]*\\\\)?Test\b|@test\b/', $function['prefix']) === 1;
    }

    /** @param list<string> $body */
    public static function assertions(array $body): int
    {
        $count = 0;
        foreach ($body as $i => $text) {
            $call = ($body[$i + 1] ?? null) === '(';
            $member = in_array($body[$i - 1] ?? null, ['->', '::', '?->'], true);
            if ($call && (($member && (str_starts_with($text, 'assert') || str_starts_with($text, 'expectException'))) || (! $member && $text === 'expect'))) {
                $count++;
            }
        }

        return $count;
    }

    /** @param list<string> $body */
    public static function skipped(array $body): bool
    {
        return array_intersect($body, ['markTestSkipped', 'markTestIncomplete']) !== [];
    }

    /** @return list<array{name:string, kind:string, implements:list<string>, methods:list<string>}> */
    public static function classes(string $source): array
    {
        $tokens = self::tokens($source, false);
        $classes = [];
        foreach ($tokens as $i => $token) {
            if (! $token->is([T_CLASS, T_INTERFACE]) || ($tokens[$i - 1] ?? null)?->is([T_DOUBLE_COLON, T_NEW]) || ! ($tokens[$i + 1] ?? null)?->is(T_STRING)) {
                continue;
            }
            $implements = [];
            $collect = false;
            for ($j = $i + 2; isset($tokens[$j]) && $tokens[$j]->text !== '{'; $j++) {
                if ($tokens[$j]->is(T_IMPLEMENTS) || ($token->is(T_INTERFACE) && $tokens[$j]->is(T_EXTENDS))) {
                    $collect = true;
                } elseif ($collect && $tokens[$j]->is([T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED])) {
                    $implements[] = basename(str_replace('\\', '/', $tokens[$j]->text));
                }
            }
            $classes[] = ['name' => $tokens[$i + 1]->text, 'kind' => $token->is(T_INTERFACE) ? 'interface' : 'class', 'implements' => $implements, 'methods' => []];
        }
        foreach (self::functions($source) as $function) {
            foreach ($classes as $index => $class) {
                if ($class['name'] === $function['class']) {
                    $classes[$index]['methods'][] = $function['name'];
                }
            }
        }

        return $classes;
    }

    /** @return array<string, list<string>> table => columns from Schema::create/table blueprints */
    public static function migrationColumns(string $source): array
    {
        $tokens = self::tokens($source, false);
        $tables = [];
        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            if (! ($tokens[$i]->text === 'Schema' && ($tokens[$i + 1] ?? null)?->is(T_DOUBLE_COLON) && in_array($tokens[$i + 2]->text ?? '', ['create', 'table'], true)
                && ($tokens[$i + 3]->text ?? '') === '(' && ($tokens[$i + 4] ?? null)?->is(T_CONSTANT_ENCAPSED_STRING))) {
                continue;
            }
            $table = self::literal($tokens[$i + 4]->text);
            $start = null;
            for ($j = $i + 5; $j < $count; $j++) {
                if ($tokens[$j]->text === '{') {
                    $start = $j;
                    break;
                }
            }
            if ($start === null) {
                continue;
            }
            $columns = $tables[$table] ?? [];
            $level = 0;
            for ($j = $start; $j < $count; $j++) {
                $text = $tokens[$j]->text;
                if ($text === '{') {
                    $level++;
                } elseif ($text === '}' && --$level === 0) {
                    break;
                }
                if (! ($tokens[$j]->is(T_VARIABLE) && ($tokens[$j + 1] ?? null)?->is(T_OBJECT_OPERATOR) && ($tokens[$j + 3]->text ?? '') === '(')) {
                    continue;
                }
                $method = $tokens[$j + 2]->text;
                $argument = ($tokens[$j + 4] ?? null)?->is(T_CONSTANT_ENCAPSED_STRING) ? self::literal($tokens[$j + 4]->text) : null;
                if (in_array($method, self::COLUMN_TYPES, true) && $argument !== null) {
                    $columns[] = $argument;
                } elseif ($method === 'id') {
                    $columns[] = $argument ?? 'id';
                } elseif (in_array($method, ['timestamps', 'timestampsTz', 'nullableTimestamps'], true)) {
                    array_push($columns, 'created_at', 'updated_at');
                } elseif (in_array($method, ['softDeletes', 'softDeletesTz'], true)) {
                    $columns[] = $argument ?? 'deleted_at';
                } elseif (in_array($method, ['morphs', 'nullableMorphs', 'uuidMorphs', 'nullableUuidMorphs', 'ulidMorphs', 'nullableUlidMorphs'], true) && $argument !== null) {
                    array_push($columns, $argument.'_type', $argument.'_id');
                } elseif ($method === 'rememberToken') {
                    $columns[] = 'remember_token';
                } elseif ($method === 'dropColumn' && $argument !== null) {
                    $columns = array_values(array_diff($columns, [$argument]));
                } elseif ($method === 'renameColumn' && $argument !== null && ($tokens[$j + 6] ?? null)?->is(T_CONSTANT_ENCAPSED_STRING)) {
                    $columns = array_values(array_diff($columns, [$argument]));
                    $columns[] = self::literal($tokens[$j + 6]->text);
                }
            }
            $tables[$table] = array_values(array_unique($columns));
            $i = $j;
        }

        return $tables;
    }

    /** @return list<PhpToken> */
    private static function tokens(string $source, bool $keepDocComments): array
    {
        $skip = $keepDocComments ? [T_WHITESPACE, T_COMMENT] : [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT];

        return array_values(array_filter(PhpToken::tokenize($source), fn (PhpToken $token) => ! $token->is($skip)));
    }

    private static function literal(string $text): string
    {
        return stripcslashes(substr($text, 1, -1));
    }
}
