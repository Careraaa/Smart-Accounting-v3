<?php
/**
 * verify-target-dumps.php
 * Prove that every generated export has the SAME structure and a valid
 * migrations table, so any of them can be imported into any branch.
 *
 *   php database/manual/verify-target-dumps.php <dump.sql> [...]
 */
$files = $argv;
array_shift($files);
if (!$files) { fwrite(STDERR, "usage: verify-target-dumps.php <dump.sql> [...]\n"); exit(1); }

function tables(string $path): array {
    $sql = file_get_contents($path);
    $sql = str_replace("\r\n", "\n", $sql);
    $sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql);
    $t = [];
    $cur = null; $buf = '';
    foreach (explode("\n", $sql) as $l) {
        if ($cur === null && preg_match('/^CREATE TABLE `([^`]+)`/', $l, $m)) {
            $cur = $m[1]; $buf = $l;
            if (preg_match('/^\s*\)\s*(ENGINE=[^;]*)?\s*;\s*$/', $l)) { $t[$cur] = cols($buf); $cur = null; }
            continue;
        }
        if ($cur !== null) {
            $buf .= "\n" . $l;
            if (preg_match('/^\s*\)\s*(ENGINE=[^;]*)?\s*;\s*$/', $l)) { $t[$cur] = cols($buf); $cur = null; }
        }
    }
    return $t;
}
function cols(string $block): array {
    $cols = [];
    if (preg_match('/\((.*)\)\s*ENGINE=/s', $block, $m)) {
        foreach (explode("\n", $m[1]) as $c) {
            $c = trim($c);
            if (preg_match('/^`([^`]+)`\s+(.*)$/', $c, $cc)) $cols[$cc[1]] = trim($cc[2]);
        }
    }
    return $cols;
}

$ref = null; $refName = null; $allOk = true;

foreach ($files as $f) {
    $name = basename($f);
    $sql = file_get_contents($f);
    $sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql);
    $t = tables($f);

    $profile = 'unknown';
    if (preg_match('/-- TARGET DUMP: (\w+)/', $sql, $m)) $profile = $m[1];
    // "Data rule : full" or "reference + staff only" - the staff-only rule
    // only applies when the dump was built without the client's full data.
    $dataRule = 'full';
    if (preg_match('/-- Data rule : (.+)/', $sql, $m)) $dataRule = trim($m[1]);

    $ct = preg_match_all('/^CREATE TABLE/m', $sql);
    $dt = preg_match_all('/^DROP TABLE IF EXISTS/m', $sql);
    $migRows = preg_match_all("/\(\d+,'[^']+',1\)/", $sql);
    $users = preg_match('/INSERT INTO `users`/i', $sql);
    preg_match_all('/^INSERT INTO `([^`]+)`/m', $sql, $im);
    $insTables = array_values(array_unique($im[1]));
    $userRows = 0;
    if (preg_match('/INSERT INTO `users`[\s\S]*?;/', $sql, $um)) {
        $userRows = preg_match_all('/\),\s*\(/', $um[0]) + 1;
    }

    echo "\n=== $name  (profile: $profile) ===\n";
    printf("  tables=%d  drop=%d  migrations_rows=%d  users=%d\n", count($t), $dt, $migRows, $userRows);
    printf("  data tables: %s\n", implode(', ', $insTables));

    $problems = [];
    if (count($t) !== 50) $problems[] = "expected 50 tables, got " . count($t);
    if ($ct !== 50)       $problems[] = "expected 50 CREATE TABLE, got $ct";
    if ($dt !== 50)       $problems[] = "expected 50 DROP TABLE, got $dt";
    if ($migRows !== 26)  $problems[] = "expected 26 migration rows, got $migRows";
    if (!$users)          $problems[] = "no users INSERT";
    if ($profile === 'sqa' && $dataRule !== 'full' && $userRows !== 5) $problems[] = "sqa staff-only dump must have exactly 5 accounts, got $userRows";
    if (strpos($sql, 'SET FOREIGN_KEY_CHECKS = 0;') === false) $problems[] = "no FK-disable at start";
    if (strpos($sql, 'SET FOREIGN_KEY_CHECKS = 1;') === false) $problems[] = "no FK-enable at end";

    // Demo data is only a problem for a staff-only (clean) dump.
    if ($profile === 'sqa' && $dataRule !== 'full') {
        $demoLeak = array_values(array_intersect($insTables, [
            'payrolls', 'attendance', 'daily_remittances', 'drivers', 'paos',
            'leave_requests', 'cash_advances', 'employees', 'notifications',
            'cache', 'jobs', 'sessions', 'user_activities', 'payroll_batches',
            'attendance_logs', 'overtime_undertimes', 'vehicles', 'routes',
        ]));
        if ($demoLeak) $problems[] = "demo data present: " . implode(', ', $demoLeak);
    }

    // Foreign keys: a parent table must be inserted before its child,
    // otherwise the import fails with MySQL error #1452.
    $fks = [];
    if (preg_match_all('/CREATE TABLE `([^`]+)`\s*\([\s\S]*?\n\)\s*(ENGINE=[^;]*)?\s*;/', $sql, $tb, PREG_SET_ORDER)) {
        foreach ($tb as $b) {
            if (preg_match_all('/FOREIGN KEY \(`[^`]+`\) REFERENCES `([^`]+)`/', $b[0], $m)) {
                foreach ($m[1] as $p) if ($p !== $b[1]) $fks[$b[1]][] = $p;
            }
        }
    }
    $pos = [];
    foreach ($insTables as $i => $tblName) $pos[$tblName] = $i;
    foreach ($fks as $child => $parents) {
        if (!isset($pos[$child])) continue;                 // child has no data
        foreach ($parents as $parent) {
            if (!isset($pos[$parent])) continue;            // parent has no data
            if ($pos[$parent] > $pos[$child]) {
                $problems[] = "FK order: '$parent' must be inserted before '$child'";
            }
        }
    }

    if ($ref === null) { $ref = $t; $refName = $name; }
    else {
        foreach ($ref as $tbl => $cols2) {
            if (!isset($t[$tbl])) { $problems[] = "table missing vs $refName: $tbl"; continue; }
            foreach ($cols2 as $c => $def) {
                if (!isset($t[$tbl][$c])) $problems[] = "column missing vs $refName: $tbl.$c";
            }
        }
        foreach (array_keys($t) as $tbl) {
            if (!isset($ref[$tbl])) $problems[] = "extra table vs $refName: $tbl";
        }
    }

    if ($problems) { $allOk = false; foreach ($problems as $p) echo "  PROBLEM: $p\n"; }
    else echo "  OK\n";
}

echo "\n" . ($allOk ? "ALL DUMPS COMPATIBLE\n" : "PROBLEMS FOUND\n");
exit($allOk ? 0 : 1);