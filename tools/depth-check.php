<?php
declare(strict_types=1);

/**
 * Depth Gate Checker for ERP modules.
 * Usage:
 *   php tools/depth-check.php --all [--json] [--gate] [--write-docs]
 *   php tools/depth-check.php <module-id> [--json] [--gate]
 */

$root = dirname(__DIR__);
$args = array_slice($argv, 1);
$all = in_array('--all', $args, true);
$asJson = in_array('--json', $args, true);
$gateMode = in_array('--gate', $args, true);
$writeDocs = in_array('--write-docs', $args, true);

$targetId = null;
foreach ($args as $a) {
    if (!str_starts_with($a, '--')) {
        $targetId = $a;
        break;
    }
}

if (!$all && $targetId === null) {
    fwrite(STDERR, "Usage: php tools/depth-check.php [<module-id>|--all] [--json] [--gate] [--write-docs]\n");
    exit(2);
}

$manifestFiles = $all
    ? (glob($root . '/modules/*/module.json') ?: [])
    : [$root . '/modules/' . $targetId . '/module.json'];

sort($manifestFiles);

$results = [];
$offenders = [];

foreach ($manifestFiles as $mfile) {
    if (!is_file($mfile)) {
        fwrite(STDERR, "ERROR: Module manifest not found: $mfile\n");
        exit(2);
    }
    $mdir = dirname($mfile);
    $id = basename($mdir);
    $manifest = json_decode((string) file_get_contents($mfile), true) ?: [];
    $status = (string) ($manifest['status'] ?? 'stub');

    $specFile = $root . '/specs/' . $id . '.md';
    $specText = is_file($specFile) ? (string) file_get_contents($specFile) : '';

    // Gather module files
    $phpFiles = glob_recursive($mdir . '/backend', '*.php');
    $sqlFiles = glob_recursive($mdir . '/backend/migrations', '*.sql');
    $tsFiles = glob_recursive($mdir . '/web', '*.{ts,tsx}');
    $testFiles = glob_recursive($mdir . '/backend/tests', '*Test.php');

    $sqlText = '';
    foreach ($sqlFiles as $f) {
        $sqlText .= "\n" . file_get_contents($f);
    }
    $phpSrcText = '';
    foreach ($phpFiles as $f) {
        if (!str_contains($f, '/tests/')) {
            $phpSrcText .= "\n" . file_get_contents($f);
        }
    }
    $testText = '';
    foreach ($testFiles as $f) {
        $testText .= "\n" . file_get_contents($f);
    }

    $missing = [];

    // 1. entity_cov (weight 20)
    $entities = $manifest['entities'] ?? [];
    if (empty($entities) && $specText !== '') {
        if (preg_match('/##\s*Entities.*?\n(.*?)(?=\n##|\z)/is', $specText, $m)) {
            preg_match_all('/^[-*]\s+`?([a-z0-9_]+)/im', $m[1], $em);
            $entities = $em[1] ?? [];
        }
    }
    $sqlLower = strtolower($sqlText);
    $entHit = 0;
    foreach ($entities as $ent) {
        $e = strtolower((string) $ent);
        $ePlural = str_ends_with($e, 'y') ? substr($e, 0, -1) . 'ies' : $e . 's';
        if (str_contains($sqlLower, $e) || str_contains($sqlLower, $ePlural)) {
            $entHit++;
        } else {
            $missing[] = "entity:$ent";
        }
    }
    $entityCov = count($entities) > 0 ? (int) round(100 * $entHit / count($entities)) : (preg_match_all('/CREATE\s+TABLE/i', $sqlText) > 0 ? 50 : 0);

    // 2. event_cov (weight 15)
    $emits = $manifest['emits'] ?? [];
    $listens = $manifest['listens'] ?? [];
    $evTotal = count($emits) + count($listens);
    $evHit = 0;
    $phpLower = strtolower($phpSrcText);
    foreach ($emits as $ev) {
        if (str_contains($phpLower, strtolower((string) $ev))) {
            $evHit++;
        } else {
            $missing[] = "emit:$ev";
        }
    }
    $listenersFile = $mdir . '/backend/listeners.php';
    $listenersText = is_file($listenersFile) ? strtolower((string) file_get_contents($listenersFile)) : '';
    foreach ($listens as $ev) {
        if ($listenersText !== '' && str_contains($listenersText, strtolower((string) $ev))) {
            $evHit++;
        } else {
            $missing[] = "listen:$ev";
        }
    }
    $eventCov = $evTotal > 0 ? (int) round(100 * $evHit / $evTotal) : 100;

    // 3. contract_cov (weight 20)
    $contractFiles = glob_recursive($mdir . '/backend/src/Contracts', '*.php');
    $contractMethods = 0;
    $contractImpl = 0;
    foreach ($contractFiles as $cf) {
        $ct = (string) file_get_contents($cf);
        preg_match_all('/function\s+([a-zA-Z0-9_]+)\s*\(/', $ct, $cm);
        foreach ($cm[1] as $method) {
            $contractMethods++;
            if (preg_match('/function\s+' . preg_quote($method, '/') . '\s*\([^)]*\)[^{]*\{(.*?)\n\s*\}/s', $phpSrcText, $bm)) {
                $body = trim($bm[1]);
                $isTrivial = $body === ''
                    || preg_match('/^(?:return\s+(?:null|\[\]|0|\'\'|""|true|false);|throw\s+)/i', $body) === 1
                    || stripos($body, 'not implemented') !== false;
                if (!$isTrivial) {
                    $contractImpl++;
                } else {
                    $missing[] = "contract_trivial:$method";
                }
            } else {
                $missing[] = "contract_missing:$method";
            }
        }
    }
    $contractCov = $contractMethods > 0 ? (int) round(100 * $contractImpl / $contractMethods) : 100;

    // 4. rule_cov (weight 20)
    $ruleCount = 0;
    if (preg_match('/##\s*Rules.*?\n(.*?)(?=\n##|\z)/is', $specText, $rm)) {
        preg_match_all('/^[-*\d.]+\s+/m', $rm[1], $rlm);
        $ruleCount = count($rlm[0]);
    }
    $assertions = preg_match_all('/\bassert(?:True|Equals)\s*\(/', $testText);
    $targetAssertions = max(2, $ruleCount * 2);
    $ruleCov = $targetAssertions > 0 ? min(100, (int) round(100 * $assertions / $targetAssertions)) : 0;
    if ($ruleCov < 60) {
        $missing[] = "rules_tests:$assertions/$targetAssertions";
    }

    // 5. perm_cov (weight 10)
    $perms = $manifest['permissions'] ?? [];
    $routesText = is_file($mdir . '/backend/routes.php') ? (string) file_get_contents($mdir . '/backend/routes.php') : '';
    $webText = '';
    foreach ($tsFiles as $tf) {
        $webText .= "\n" . file_get_contents($tf);
    }
    $permHit = 0;
    foreach ($perms as $p) {
        $inRoute = str_contains($routesText, (string) $p) || str_contains($phpSrcText, (string) $p);
        $inWeb = str_contains($webText, (string) $p);
        if ($inRoute && $inWeb) {
            $permHit++;
        } else {
            $missing[] = "perm:$p";
        }
    }
    $permCov = count($perms) > 0 ? (int) round(100 * $permHit / count($perms)) : 100;

    // 6. ui_cov (weight 10)
    $pagesCount = count(glob($mdir . '/web/pages/*.tsx') ?: []);
    $hasRoutesTs = is_file($mdir . '/web/routes.ts');
    $hasFa = is_file($mdir . '/web/i18n/fa.json');
    $uiScore = 0;
    if ($pagesCount >= 3) {
        $uiScore += 60;
    } elseif ($pagesCount >= 1) {
        $uiScore += 30;
        $missing[] = "ui_pages:$pagesCount/3";
    } else {
        $missing[] = "ui_pages:0/3";
    }
    if ($hasRoutesTs) {
        $uiScore += 20;
    } else {
        $missing[] = "ui_routes";
    }
    if ($hasFa) {
        $uiScore += 20;
    } else {
        $missing[] = "ui_i18n_fa";
    }
    $uiCov = $uiScore;

    // 7. ai_cov (weight 5)
    $aiCaps = $manifest['ai']['capabilities'] ?? [];
    if (empty($aiCaps)) {
        $aiCov = 100;
    } else {
        $hasOffTest = stripos($testText, 'enabled') !== false || stripos($testText, 'off') !== false;
        $aiCov = $hasOffTest ? 100 : 20;
        if (!$hasOffTest) {
            $missing[] = "ai_tests";
        }
    }

    // Anti-gaming checks
    $antiGaming = [];
    if (preg_match('/\b(TODO|FIXME|NotImplemented|not implemented|placeholder|lorem|dummy)\b/i', $phpSrcText)) {
        $antiGaming[] = 'banned_marker_in_php';
    }
    if (preg_match('/assertTrue\s*\(\s*true\s*\)/i', $testText)) {
        $antiGaming[] = 'trivial_assertion';
    }

    $score = (int) round(
        $entityCov * 0.20 +
        $eventCov * 0.15 +
        $contractCov * 0.20 +
        $ruleCov * 0.20 +
        $permCov * 0.10 +
        $uiCov * 0.10 +
        $aiCov * 0.05
    );

    $minCat = min($entityCov, $eventCov, $contractCov, $ruleCov, $permCov, $uiCov, $aiCov);
    $pass = $score >= 80 && $minCat >= 60 && empty($antiGaming);

    if ($gateMode && $status === 'done' && !$pass) {
        $offenders[] = [
            'id' => $id,
            'score' => $score,
            'min_cat' => $minCat,
            'anti_gaming' => $antiGaming,
            'missing' => array_slice($missing, 0, 8),
        ];
    }

    $results[] = [
        'id' => $id,
        'status' => $status,
        'score' => $score,
        'pass' => $pass,
        'entity_cov' => $entityCov,
        'event_cov' => $eventCov,
        'contract_cov' => $contractCov,
        'rule_cov' => $ruleCov,
        'perm_cov' => $permCov,
        'ui_cov' => $uiCov,
        'ai_cov' => $aiCov,
        'anti_gaming' => $antiGaming,
        'missing' => $missing,
    ];
}

if ($writeDocs && $all) {
    $md = [
        "# Depth Status (`docs/depth-status.md`)",
        "",
        "Generated by `tools/depth-check.php --all --write-docs`.",
        "",
        "| Module | Status | Score | Entity | Event | Contract | Rule | Perm | UI | AI | Pass | Missing (top) |",
        "|---|---|---:|---:|---:|---:|---:|---:|---:|---:|---|---|",
    ];
    foreach ($results as $r) {
        $miss = implode(', ', array_slice($r['missing'], 0, 5));
        $md[] = sprintf(
            "| `%s` | %s | %d | %d%% | %d%% | %d%% | %d%% | %d%% | %d%% | %d%% | %s | %s |",
            $r['id'], $r['status'], $r['score'],
            $r['entity_cov'], $r['event_cov'], $r['contract_cov'],
            $r['rule_cov'], $r['perm_cov'], $r['ui_cov'], $r['ai_cov'],
            $r['pass'] ? 'PASS' : 'FAIL', $miss
        );
    }
    file_put_contents($root . '/docs/depth-status.md', implode("\n", $md) . "\n");
}

if ($asJson) {
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    foreach ($results as $r) {
        echo sprintf(
            "%-28s status=%-7s score=%3d%% (ent=%3d evt=%3d ctr=%3d rul=%3d prm=%3d ui=%3d ai=%3d) %s\n",
            $r['id'], $r['status'], $r['score'],
            $r['entity_cov'], $r['event_cov'], $r['contract_cov'],
            $r['rule_cov'], $r['perm_cov'], $r['ui_cov'], $r['ai_cov'],
            $r['pass'] ? 'PASS' : 'FAIL'
        );
    }
}

if ($gateMode && !empty($offenders)) {
    fwrite(STDERR, "\nDEPTH GATE FAILED: " . count($offenders) . " module(s) marked 'done' without passing:\n");
    foreach ($offenders as $o) {
        fwrite(STDERR, " - {$o['id']} (score={$o['score']}%, min_cat={$o['min_cat']}%)\n");
    }
    exit(1);
}

exit(0);

/** @return array<int, string> */
function glob_recursive(string $dir, string $pattern): array
{
    if (!is_dir($dir)) {
        return [];
    }
    $files = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file->isFile() && fnmatch($pattern, $file->getFilename(), FNM_BRACE)) {
            $files[] = $file->getPathname();
        }
    }
    sort($files);
    return $files;
}
