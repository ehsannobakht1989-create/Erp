<?php

declare(strict_types=1);

namespace Erp\Depth;

/**
 * Extracts gate requirements from the three spec layouts in specs/: compact bold labels
 * (`**Entities** Name{field,...}`), labelled bullet lists, and Persian `##` sections.
 */
final class SpecParser
{
    public const AI_CAPABILITIES = ['assistant', 'summarize', 'autofill', 'anomaly', 'forecast', 'nl_report', 'next_action', 'ocr'];

    private const LABELS = [
        'entities' => 'entities', 'rules' => 'rules', 'business rules' => 'rules',
        'events out' => 'events_out', 'emits' => 'events_out', 'in' => 'events_in', 'events in' => 'events_in', 'listens' => 'events_in',
        'service contract' => 'contract', 'permissions' => 'permissions', 'screens' => 'screens', 'ai' => 'ai',
        'iran' => 'other', 'datasets' => 'other', 'datasets exposed' => 'other', 'datasets consumed' => 'other', 'eval' => 'other', 'workflow' => 'other',
        'python capability' => 'other', 'purpose' => 'other', 'edge' => 'other', 'exposed' => 'other', 'events' => 'other', 'other' => 'other',
    ];

    private const HEADERS = ['موجودیت' => 'Entities', 'قوانین' => 'Rules', 'رویداد' => 'Events', 'قرارداد سرویس' => 'Service contract', 'دسترسی' => 'Permissions', 'هوش مصنوعی' => 'AI', 'صفحات' => 'Screens'];

    /**
     * @param  list<string>  $knownModules
     * @return array{entities: list<array{name:string, table:?string, fields:list<string>}>, rules: list<string>, events_out: list<string>, events_in: list<string>, contract: list<string>, permissions: list<string>, ai: list<string>}
     */
    public static function parse(string $markdown, string $moduleId, array $knownModules): array
    {
        $sections = self::sections($markdown);
        preg_match_all('/\b('.implode('|', self::AI_CAPABILITIES).')\b/', $sections['ai'] ?? '', $ai);

        return [
            'entities' => self::entities($sections['entities'] ?? ''),
            'rules' => self::rules($sections['rules'] ?? ''),
            'events_out' => self::events($sections['events_out'] ?? '', $moduleId, $knownModules),
            'events_in' => self::events($sections['events_in'] ?? '', null, $knownModules),
            'contract' => self::contract($sections['contract'] ?? ''),
            'permissions' => self::permissions($sections['permissions'] ?? '', $moduleId),
            'ai' => array_values(array_unique($ai[1])),
        ];
    }

    /** @return array<string, string> */
    private static function sections(string $markdown): array
    {
        $text = (string) preg_replace_callback('/^#{2,3}\s+(.+)$/mu', function (array $match): string {
            foreach (self::HEADERS as $needle => $label) {
                if (str_contains($match[1], $needle)) {
                    return '**'.$label.'**';
                }
            }

            return '**Other**';
        }, $markdown);
        preg_match_all('/\*\*([A-Za-z][A-Za-z &]*?)\s*(?:\([^*]*\))?\s*:?\s*\*\*\s*:?/u', $text, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        $sections = [];
        $current = null;
        $start = 0;
        foreach ($matches as $match) {
            $label = self::LABELS[strtolower(trim($match[1][0]))] ?? null;
            if ($label === null) {
                continue; // Bold text that is not a section label (e.g. an entity name) stays content.
            }
            if ($current !== null) {
                $sections[$current] = ($sections[$current] ?? '')."\n".substr($text, $start, $match[0][1] - $start);
            }
            $current = $label;
            $start = $match[0][1] + strlen($match[0][0]);
        }
        if ($current !== null) {
            $sections[$current] = ($sections[$current] ?? '')."\n".substr($text, $start);
        }

        return $sections;
    }

    /** @return list<array{name:string, table:?string, fields:list<string>}> */
    private static function entities(string $content): array
    {
        $entities = [];
        $bullets = self::bullets($content);
        if ($bullets === []) {
            preg_match_all('/(?<![A-Za-z0-9_])([A-Z][A-Za-z0-9]*)(?:\s*\([^(){}]*\))?\s*\{/u', $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
            foreach ($matches as $match) {
                $open = $match[0][1] + strlen($match[0][0]);
                $depth = 1;
                for ($end = $open; $end < strlen($content) && $depth > 0; $end++) {
                    $depth += match ($content[$end]) {
                        '{' => 1, '}' => -1, default => 0
                    };
                }
                if ($depth !== 0) {
                    continue;
                }
                $fields = [];
                foreach (self::split(substr($content, $open, $end - $open - 1), [',']) as $token) {
                    $fields = array_merge($fields, self::fieldNames($token));
                }
                $entities[] = ['name' => $match[1][0], 'table' => null, 'fields' => array_values(array_unique($fields))];
            }

            return $entities;
        }
        foreach ($bullets as $bullet) {
            if (preg_match('/^\*\*([A-Z][A-Za-z0-9 ]*?)\s*(?:\(`([a-z0-9_]+)`\))?\s*\*\*\s*:?(.*)$/su', $bullet, $bold) === 1) {
                $entities[] = ['name' => str_replace(' ', '', $bold[1]), 'table' => $bold[2] !== '' ? $bold[2] : null, 'fields' => []];
                $bullet = $bold[3];
            }
            preg_match_all('/`([^`]+)`/u', $bullet, $ticks);
            foreach ($ticks[1] as $token) {
                if (preg_match('/^[A-Z][A-Za-z0-9]*[a-z][A-Za-z0-9]*$/', $token) === 1) {
                    $entities[] = ['name' => $token, 'table' => null, 'fields' => []];
                } elseif ($entities !== [] && ! str_contains($token, '|')) {
                    $last = count($entities) - 1;
                    $entities[$last]['fields'] = array_values(array_unique(array_merge($entities[$last]['fields'], self::fieldNames($token))));
                }
            }
        }

        return $entities;
    }

    /** @return list<string> */
    private static function fieldNames(string $token): array
    {
        do {
            $token = (string) preg_replace('/\[[^\[\]]*\]|\([^()]*\)|\{[^{}]*\}/', '', $token, -1, $replaced);
        } while ($replaced > 0);
        $token = trim(str_replace('?', '', $token), " \t*`");
        $names = [];
        foreach (explode('+', $token) as $piece) {
            $alternatives = explode('/', trim($piece));
            $first = (string) array_shift($alternatives);
            $names[] = $first;
            foreach ($alternatives as $alternative) {
                $names[] = str_contains($alternative, '_') || ! str_contains($first, '_') ? $alternative : (string) preg_replace('/_[^_]*$/', '_'.$alternative, $first);
            }
        }

        return array_values(array_filter(array_map('trim', $names), fn (string $name) => preg_match('/^[a-z][a-z0-9_]*$/', $name) === 1));
    }

    /** @return list<string> */
    private static function rules(string $content): array
    {
        $rules = self::bullets($content);
        if ($rules === []) {
            $rules = self::split((string) preg_replace('/\s+/u', ' ', $content), [';', '؛']);
        }

        return array_values(array_filter(array_map(fn (string $rule) => trim($rule, " \t\n.-*"), $rules), fn (string $rule) => mb_strlen($rule) >= 6));
    }

    /**
     * @param  list<string>  $knownModules
     * @return list<string>
     */
    private static function events(string $content, ?string $prefix, array $knownModules): array
    {
        $content = (string) preg_replace('/\{[^{}]*\}/u', '', $content);
        preg_match_all('/(?<![A-Za-z0-9_.\-|])(\*|[a-z][a-z0-9_|-]*)((?:\.[a-z0-9_*|-]+)+)/', $content, $matches, PREG_SET_ORDER);
        $events = [];
        foreach ($matches as $match) {
            $segments = explode('.', $match[1].$match[2]);
            $short = array_filter($segments, fn (string $segment) => $segment !== '*' && strlen(trim($segment, '|')) < 2);
            if ($short !== []) {
                continue;
            }
            if ($prefix !== null) {
                if ($segments[0] !== $prefix) {
                    array_unshift($segments, $prefix);
                }
            } elseif (count($segments) < 3 || ($segments[0] !== '*' && ! in_array($segments[0], $knownModules, true))) {
                continue;
            }
            foreach (self::expand($segments) as $event) {
                if (preg_match('/^(\*|[a-z][a-z0-9-]*)(\.[a-z0-9_*]+){2,}$/', $event) === 1) {
                    $events[] = $event;
                }
            }
        }

        return array_values(array_unique($events));
    }

    /** @return list<string> */
    private static function contract(string $content): array
    {
        $content = (string) preg_replace('/\([^()]*\)/', '()', $content);
        preg_match_all('/(?<![\w$>:])([a-z][A-Za-z0-9_]*)\s*\(|`([a-z][A-Za-z0-9_]*)`/', $content, $matches, PREG_SET_ORDER);
        $methods = [];
        foreach ($matches as $match) {
            $name = ($match[1] ?? '') !== '' ? $match[1] : ($match[2] ?? '');
            if ($name !== '' && ! in_array($name, ['array', 'list', 'isset', 'empty'], true)) {
                $methods[] = $name;
            }
        }

        return array_values(array_unique($methods));
    }

    /** @return list<string> */
    private static function permissions(string $content, string $moduleId): array
    {
        preg_match_all('/[a-z][a-z0-9-]*(?:\.[a-z0-9_|-]+)+/', $content, $matches);
        $permissions = [];
        foreach ($matches[0] as $token) {
            $segments = explode('.', $token);
            if ($segments[0] === $moduleId && count($segments) >= 2) {
                $permissions = array_merge($permissions, self::expand($segments));
            }
        }

        return array_values(array_unique(array_filter($permissions, fn (string $p) => preg_match('/^[a-z][a-z0-9-]*(\.[a-z0-9_]+)+$/', $p) === 1)));
    }

    /**
     * Cross product of `a|b` alternatives in each dotted segment.
     *
     * @param  list<string>  $segments
     * @return list<string>
     */
    private static function expand(array $segments): array
    {
        $results = [''];
        foreach ($segments as $index => $segment) {
            $next = [];
            foreach ($results as $partial) {
                foreach (array_filter(explode('|', $segment), fn (string $part) => $part !== '') as $alternative) {
                    $next[] = $index === 0 ? $alternative : $partial.'.'.$alternative;
                }
            }
            $results = $next;
        }

        return $results;
    }

    /** @return list<string> */
    private static function bullets(string $content): array
    {
        $bullets = [];
        foreach (preg_split('/\R/u', $content) ?: [] as $line) {
            if (preg_match('/^\s*[-*]\s+(.+)$/u', $line, $match) === 1) {
                $bullets[] = trim($match[1]);
            }
        }

        return $bullets;
    }

    /**
     * Split on separators outside (), [] and {}.
     *
     * @param  list<string>  $separators
     * @return list<string>
     */
    private static function split(string $text, array $separators): array
    {
        $items = [];
        $depth = 0;
        $current = '';
        foreach (mb_str_split($text) as $char) {
            if (in_array($char, ['(', '[', '{'], true)) {
                $depth++;
            } elseif (in_array($char, [')', ']', '}'], true)) {
                $depth = max(0, $depth - 1);
            } elseif ($depth === 0 && in_array($char, $separators, true)) {
                $items[] = $current;
                $current = '';

                continue;
            }
            $current .= $char;
        }
        $items[] = $current;

        return array_values(array_filter(array_map('trim', $items), fn (string $item) => $item !== ''));
    }
}
