<?php
/**
 * Compares the schema Laravel builds from the migrations (database "ref")
 * against the schema in the repaired dump (database "got").
 *
 * Any column in ref but missing from got is a column the app needs and the
 * database does not have — which is what causes the 500 errors.
 */

$pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=information_schema', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$cols = function (string $schema) use ($pdo): array {
    $sql = "SELECT table_name, column_name, column_type, is_nullable, column_default, extra
            FROM columns
            WHERE table_schema = '$schema'
            ORDER BY table_name, ordinal_position";
    $out = [];
    foreach ($pdo->query($sql) as $r) {
        $out[$r['table_name']][$r['column_name']] = [
            $r['column_type'], $r['is_nullable'], $r['column_default'], $r['extra'],
        ];
    }
    return $out;
};

$ref = $cols('ref');
$got = $cols('got');

$missingTables = array_diff(array_keys($ref), array_keys($got));
$extraTables   = array_diff(array_keys($got), array_keys($ref));

if ($missingTables) {
    echo "MISSING TABLES (app needs them, database lacks them):\n";
    foreach ($missingTables as $t) {
        echo "  - $t\n";
    }
    echo "\n";
}
if ($extraTables) {
    echo "EXTRA TABLES (in database, not created by migrations):\n";
    foreach ($extraTables as $t) {
        echo "  - $t\n";
    }
    echo "\n";
}

echo "MISSING COLUMNS:\n";
$any = false;
foreach ($ref as $table => $cols) {
    if (!isset($got[$table])) {
        continue;
    }
    foreach ($cols as $name => $def) {
        if (!isset($got[$table][$name])) {
            printf("  %-28s %-26s %s null=%s default=%s\n",
                $table, $name, $def[0], $def[1],
                $def[2] === null ? 'NULL' : $def[2]);
            $any = true;
        }
    }
}
if (!$any) {
    echo "  (none)\n";
}

echo "\nCOLUMN TYPE MISMATCHES:\n";
$any = false;
foreach ($ref as $table => $cols) {
    if (!isset($got[$table])) {
        continue;
    }
    foreach ($cols as $name => $def) {
        if (isset($got[$table][$name]) && $got[$table][$name][0] !== $def[0]) {
            printf("  %-28s %-26s ref=%-22s got=%s\n",
                $table, $name, $def[0], $got[$table][$name][0]);
            $any = true;
        }
    }
}
if (!$any) {
    echo "  (none)\n";
}