<?php

declare(strict_types=1);

namespace Erp\Depth;

/** Detect a conservative subset of trivial PHP function bodies; not a coverage proof. */
final class PhpAnalyzer
{
    /** @return list<array{line:int, name:string, reason:string}> */
    public static function trivialFunctions(string $source): array
    {
        $tokens = token_get_all($source);
        $results = [];
        $ignored = [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT];
        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            if (! is_array($tokens[$i]) || $tokens[$i][0] !== T_FUNCTION) {
                continue;
            }
            $line = $tokens[$i][2];
            $name = null;
            $start = null;
            $parenDepth = 0;
            $seenParams = false;
            for ($j = $i + 1; $j < $count; $j++) {
                $token = $tokens[$j];
                if (is_array($token) && $token[0] === T_STRING && $name === null && ! $seenParams && $parenDepth === 0) {
                    $name = $token[1];
                }
                if ($token === '(') {
                    $seenParams = true;
                    $parenDepth++;
                } elseif ($token === ')') {
                    $parenDepth--;
                } elseif ($parenDepth === 0 && $token === ';') {
                    break; // Interface or abstract method.
                } elseif ($parenDepth === 0 && $token === '{') {
                    $start = $j;
                    break;
                }
            }
            if ($name === null || $start === null) {
                continue; // Anonymous function or declaration without a body.
            }
            $depth = 1;
            $body = [];
            for ($j = $start + 1; $j < $count; $j++) {
                $token = $tokens[$j];
                if ($token === '{') {
                    $depth++;
                } elseif ($token === '}') {
                    $depth--;
                    if ($depth === 0) {
                        break;
                    }
                }
                if (! is_array($token) || ! in_array($token[0], $ignored, true)) {
                    $body[] = is_array($token) ? $token[1] : $token;
                }
            }
            if ($depth !== 0) {
                continue; // A PHP syntax check will flag malformed source separately.
            }
            $reason = null;
            if ($body === []) {
                $reason = 'empty body';
            } elseif (in_array(strtolower(implode('', $body)), ['returnnull;', 'return[];', 'returnarray();', 'return0;'], true)) {
                $reason = 'only returns a trivial value';
            } elseif (strtolower($body[0]) === 'throw' && end($body) === ';' && count(array_keys($body, ';', true)) === 1 && ! in_array('{', $body, true)) {
                $reason = 'only throws';
            }
            if ($reason !== null) {
                $results[] = ['line' => $line, 'name' => $name, 'reason' => $reason];
            }
        }

        return $results;
    }
}
