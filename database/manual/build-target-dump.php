<?php
/**
 * build-target-dump.php
 * ---------------------
 * Build a database export that is COMPATIBLE with the code on every branch.
 *
 * Why this exists
 * ---------------
 * The dumps that were checked in per branch were raw exports taken from a live
 * database at different moments, while that database was being migrated in
 * place. So each branch carried a different structure:
 *
 *   db-dropbox/db-2026-08-31_FULL_RESET_database.sql   (old - missing 10+ migrations)
 *   database/manual/test1_READY.sql                     (current - matches the code)
 *   database/sqa-deploy-structure.sql                   (current - matches the code)
 *
 * Importing the old one into a new environment produces a database whose
 * columns do not match the application, and Laravel then tries to re-create
 * tables that already exist because the `migrations` table is empty.
 *
 * How this fixes it
 * -----------------
 * 1. CANONICAL_SCHEMA.sql is the single source of truth for structure.
 *    It is always regenerated from the newest known-good dump.
 * 2. Every export is built from that canonical structure - never from the
 *    source dump's own DDL. So even a stale source yields a correct schema.
 * 3. The `migrations` table is always fully populated with every migration
 *    name, so Laravel never tries to re-run a migration.
 * 4. A profile decides how much DATA goes in.
 *
 * Usage
 * -----
 *   php database/manual/build-target-dump.php --source=<dump.sql> --profile=<sqa|test1|test2|main> [--out=<file.sql>]
 *
 * Profiles
 * --------
 *   main  | test1 | test2   full data from the source dump
 *   sqa                     reference data + 5 staff accounts only (no demo data)
 *
 * Options
 * -------
 *   --schema=<file>   canonical schema to use (default database/manual/CANONICAL_SCHEMA.sql)
 *   --make-schema     (re)generate the canonical schema from --source and exit
 *   --list            show the profiles and the tables each one keeps
 */

$repo = dirname(__DIR__, 2);
$schemaPath = $repo . '/database/manual/CANONICAL_SCHEMA.sql';

$opts = getopt('', ['source:', 'profile:', 'out:', 'schema:', 'make-schema', 'list', 'help']);
if (isset($opts['help']) || (!$opts && !isset($opts['list']))) {
    echo file_get_contents(__FILE__, false, null, 0, 1800);
    exit;
}

// â”€â”€ Profiles â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// Tables whose rows are reference/lookup data - always kept, even for sqa.
const REFERENCE_TABLES = [
    'departments', 'leave_types', 'shifts', 'holidays', 'statutory_deductions',
    'withholding_taxes', 'payroll_cutoff_schedules', 'chart_of_accounts',
    'position_rates', 'account_mappings', 'settings', 'pinned_items',
];

// Tables that must never carry demo/transactional rows into a clean env.
const NEVER_KEEP = [
    'cache', 'cache_locks', 'jobs', 'failed_jobs', 'sessions',
    'password_reset_tokens', 'password_resets', 'telescope_entries',
];

$PROFILES = [
    'main'  => ['label' => 'Production',           'data' => 'all',        'users' => 'all'],
    'test1' => ['label' => 'Test 1 (test-â€¦freedev)', 'data' => 'all',        'users' => 'all'],
    'test2' => ['label' => 'Test 2 (test2-â€¦freedev)', 'data' => 'all',      'users' => 'all'],
    'sqa'   => ['label' => 'SQA (sqa-â€¦infinityfreeapp)', 'data' => 'reference', 'users' => 5],
];

if (isset($opts['list'])) {
    echo "PROFILES\n";
    foreach ($PROFILES as $k => $p) {
        echo "  $k  ({$p['label']})\n";
        echo "      data  : {$p['data']}\n";
        echo "      users : " . ($p['users'] === 'all' ? 'all accounts' : "first {$p['users']} accounts") . "\n";
    }
    echo "\nREFERENCE TABLES (always kept)\n  " . implode(', ', REFERENCE_TABLES) . "\n";
    exit;
}

// â”€â”€ Helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

/** Split a dump into: table DDL, INSERT statements per table, standalone ALTERs. */
function parseDump(string $sql): array {
    $sql = str_replace("\r\n", "\n", $sql);
    $lines = explode("\n", $sql);
    $ddl = [];      // table => full CREATE TABLE statement
    $order = [];    // table creation order
    $data = [];     // table => list of INSERT statements
    $alters = [];

    $cur = null;
    $buf = null;
    $bufTable = null;
    $inAlter = false;

    foreach ($lines as $line) {
        // CREATE TABLE (may span many lines) -> collect until the closing ');'
        if (preg_match('/^CREATE TABLE `([^`]+)`/', $line, $m)) {
            $cur = $m[1];
            $buf = $line;
            if (preg_match('/\)\s*ENGINE=[^;]*;\s*$/', $line)) {
                $ddl[$cur] = $buf; $order[] = $cur; $cur = null; $buf = null;
            }
            continue;
        }
        if ($cur !== null) {
            $buf .= "\n" . $line;
            if (preg_match('/\)\s*ENGINE=[^;]*;\s*$/', $line)) {
                $ddl[$cur] = $buf; $order[] = $cur; $cur = null; $buf = null;
            }
            continue;
        }
        // INSERT (may span many lines)
        if (preg_match('/^INSERT INTO `([^`]+)`/', $line, $m)) {
            $bufTable = $m[1];
            $buf = $line;
            if (preg_match('/;\s*$/', $line)) {
                $data[$bufTable][] = $buf; $buf = null; $bufTable = null;
            }
            continue;
        }
        if ($bufTable !== null) {
            $buf .= "\n" . $line;
            if (preg_match('/;\s*$/', $line)) {
                $data[$bufTable][] = $buf; $buf = null; $bufTable = null;
            }
            continue;
        }
        // Standalone ALTERs are re-emitted at the end (foreign keys).
        if (preg_match('/^ALTER TABLE/', $line)) { $inAlter = true; $alters[] = $line; continue; }
        if ($inAlter) {
            $alters[] = $line;
            if (preg_match('/;\s*$/', $line)) $inAlter = false;
        }
    }
    return ['ddl' => $ddl, 'order' => $order, 'data' => $data, 'alters' => $alters];
}

/** Count top-level value tuples in an INSERT statement. */
function countTuples(string $insert): int {
    $pos = stripos($insert, 'VALUES');
    if ($pos === false) return 0;
    $body = rtrim(substr($insert, $pos + 6));
    if (substr($body, -1) === ';') $body = substr($body, 0, -1);
    $depth = 0; $inStr = false; $esc = false; $rows = 0;
    for ($i = 0, $n = strlen($body); $i < $n; $i++) {
        $c = $body[$i];
        if ($inStr) {
            if ($esc) $esc = false;
            elseif ($c === '\\') $esc = true;
            elseif ($c === "'") $inStr = false;
            continue;
        }
        if ($c === "'") { $inStr = true; continue; }
        if ($c === '(') { if ($depth === 0) $rows++; $depth++; continue; }
        if ($c === ')') $depth--;
    }
    return $rows;
}

/** Split an INSERT into its top-level tuples (values only, no parens). */
function splitTuples(string $insert): array {
    $pos = stripos($insert, 'VALUES');
    if ($pos === false) return [];
    $body = rtrim(substr($insert, $pos + 6));
    if (substr($body, -1) === ';') $body = substr($body, 0, -1);
    $tuples = []; $cur = ''; $inStr = false; $esc = false; $depth = 0;
    for ($i = 0, $n = strlen($body); $i < $n; $i++) {
        $c = $body[$i];
        if ($inStr) {
            $cur .= $c;
            if ($esc) $esc = false;
            elseif ($c === '\\') $esc = true;
            elseif ($c === "'") $inStr = false;
            continue;
        }
        if ($c === "'") { $inStr = true; $cur .= $c; continue; }
        if ($c === '(') {
            if ($depth === 0) { $cur = ''; }
            else { $cur .= $c; }
            $depth++;
            continue;
        }
        if ($c === ')') {
            $depth--;
            if ($depth === 0) { $tuples[] = $cur; $cur = ''; }
            else { $cur .= $c; }
            continue;
        }
        if ($depth > 0) $cur .= $c;
    }
    return $tuples;
}

/** Rebuild an INSERT with only the first N tuples. */
function limitInsert(string $insert, int $keep): string {
    $pos = stripos($insert, 'VALUES');
    $header = substr($insert, 0, $pos + 6);
    $tuples = splitTuples($insert);
    $tuples = array_slice($tuples, 0, $keep);
    if (!$tuples) return '';
    $last = count($tuples) - 1;
    $out = $header;
    foreach ($tuples as $i => $t) {
        $out .= ($i ? "\n" : "\n") . '(' . $t . ')' . ($i < $last ? ',' : ';');
    }
    return $out;
}

// â”€â”€ Migrations that the code on every branch contains â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function migrationNames(string $repo): array {
    $out = shell_exec('cd ' . escapeshellarg($repo) . ' && git ls-files database/migrations/');
    $names = [];
    foreach (explode("\n", (string) $out) as $f) {
        $f = trim($f);
        if ($f !== '' && str_ends_with($f, '.php')) $names[] = $f;
    }
    sort($names);
    return $names;
}

// â”€â”€ Build canonical schema â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function buildCanonical(array $parsed, array $migrations): string {
    $o = [];
    $o[] = "-- ===========================================";
    $o[] = "-- CANONICAL SCHEMA - single source of truth";
    $o[] = "-- Generated: " . date('Y-m-d H:i:s');
    $o[] = "--";
    $o[] = "-- Every branch's database export must be built from this file so";
    $o[] = "-- the structure always matches the code. Do not hand-edit.";
    $o[] = "-- ===========================================";
    $o[] = "";
    $o[] = "SET NAMES utf8mb4;";
    $o[] = "SET FOREIGN_KEY_CHECKS = 0;";
    $o[] = "";
    foreach ($parsed['order'] as $t) {
        $o[] = "DROP TABLE IF EXISTS `$t`;";
        $o[] = $parsed['ddl'][$t];
        $o[] = "";
    }
    // migrations table + rows
    if (isset($parsed['ddl']['migrations'])) {
        $vals = [];
        foreach ($migrations as $i => $m) {
            $vals[] = sprintf("(%d,'%s',1)", $i + 1, $m);
        }
        $o[] = "INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES";
        $o[] = implode(",\n", $vals) . ";";
        $o[] = "";
    }
    // foreign keys
    if ($parsed['alters']) {
        $o[] = "-- Foreign key constraints";
        foreach ($parsed['alters'] as $a) $o[] = $a;
        $o[] = "";
    }
    $o[] = "SET FOREIGN_KEY_CHECKS = 1;";
    $o[] = "";
    return implode("\n", $o);
}

// â”€â”€ Main â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

$source = $opts['source'] ?? null;
if (!$source) {
    fwrite(STDERR, "ERROR: --source=<dump.sql> is required\n");
    exit(1);
}
if (!is_file($source)) {
    fwrite(STDERR, "ERROR: source not found: $source\n");
    exit(1);
}
$profileName = $opts['profile'] ?? null;
$schemaFile = $opts['schema'] ?? $schemaPath;
$sourceSql = file_get_contents($source);
$parsed = parseDump($sourceSql);
$migrations = migrationNames($repo);

if (isset($opts['make-schema'])) {
    $canon = buildCanonical($parsed, $migrations);
    file_put_contents($schemaFile, $canon);
    printf("Canonical schema written: %s\n  %d tables, %d migrations\n", $schemaFile, count($parsed['ddl']), count($migrations));
    exit;
}

if (!$profileName || !isset($PROFILES[$profileName])) {
    fwrite(STDERR, "ERROR: --profile must be one of: " . implode(', ', array_keys($PROFILES)) . "\n");
    exit(1);
}
$profile = $PROFILES[$profileName];


if (!is_file($schemaFile)) {
    fwrite(STDERR, "ERROR: canonical schema missing: $schemaFile\n       run with --make-schema first\n");
    exit(1);
}
$canon = file_get_contents($schemaFile);

// data from the source
$keepTables = [];
if ($profile['data'] === 'all') {
    foreach (array_keys($parsed['data']) as $t) {
        if (!in_array($t, NEVER_KEEP, true)) $keepTables[] = $t;
    }
} else {
    $keepTables = REFERENCE_TABLES;
}
// `users` is always emitted - the profile decides how many accounts.
if (!in_array('users', $keepTables, true)) {
    array_unshift($keepTables, 'users');
}

$o = [];
$o[] = "-- ===========================================";
$o[] = "-- TARGET DUMP: {$profileName} ({$profile['label']})";
$o[] = "-- Generated: " . date('Y-m-d H:i:s');
$o[] = "-- Source: " . basename($source);
$o[] = "-- Structure: database/manual/CANONICAL_SCHEMA.sql (identical for all branches)";
$o[] = "-- Data rule : " . ($profile['data'] === 'all' ? 'full' : 'reference + staff only');
$o[] = "-- Migrations: " . count($migrations) . " recorded";
$o[] = "-- ===========================================";
$o[] = "";
$o[] = "SET NAMES utf8mb4;";
$o[] = "SET FOREIGN_KEY_CHECKS = 0;";
$o[] = "";

// 1. Structure (canonical - never from source)
$canonLines = explode("\n", $canon);
$skipHeader = true;
foreach ($canonLines as $line) {
    if ($skipHeader) {
        if (str_starts_with(trim($line), 'SET NAMES')) { $skipHeader = false; }
        continue;
    }
    $o[] = $line;
}
$o[] = "";

// 2. Data
$keptTables = [];
foreach ($keepTables as $t) {
    if (!isset($parsed['data'][$t])) continue;
    if ($t === 'migrations') continue;         // already written from canonical
    if ($t === 'users' && $profile['users'] !== 'all') {
        $ins = $parsed['data'][$t][0];
        $limited = limitInsert($ins, $profile['users']);
        if ($limited !== '') { $o[] = $limited; $keptTables[] = "$t (" . countTuples($limited) . ")"; }
        continue;
    }
    foreach ($parsed['data'][$t] as $ins) {
        $o[] = rtrim($ins, ';') . ';';
    }
    $keptTables[] = $t;
}
$o[] = "";
$o[] = "SET FOREIGN_KEY_CHECKS = 1;";
$o[] = "";

$out = $opts['out'] ?? ($repo . '/database/manual/target-dump-' . $profileName . '.sql');
file_put_contents($out, implode("\n", $o));

printf("Target dump written: %s\n", $out);
printf("  profile    : %s (%s)\n", $profileName, $profile['label']);
printf("  structure  : canonical (identical for every target)\n");
printf("  migrations : %d recorded\n", count($migrations));
printf("  data tables: %s\n", $keptTables ? implode(', ', $keptTables) : '(none)');
printf("  bytes      : %d\n", strlen(implode("\n", $o)));

