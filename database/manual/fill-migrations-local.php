<?php
// Fill the migrations table from the database/migrations folder (batch 1).
$dir = dirname(__DIR__) . '/migrations';
$dir = str_replace('\\', '/', $dir);
$files = glob($dir . '/*.php');
sort($files);

$lines = [];
foreach ($files as $f) {
    $name = basename($f, '.php');
    $lines[] = "('{$name}', 1)";
}

$sql = '';
$sql .= "-- Refresh migrations table from code (" . count($files) . " migrations)\n";
$sql .= "USE smart_accounting_v3;\n";
$sql .= "TRUNCATE TABLE migrations;\n";
$sql .= "INSERT INTO migrations (migration, batch) VALUES\n";
$sql .= implode(",\n", $lines);
$sql .= ";\n";

file_put_contents(__DIR__ . '/../../storage/fill-migrations.sql', $sql);
echo "written: storage/fill-migrations.sql (" . count($files) . " migrations)\n";
echo "dir={$dir} exists=" . var_export(is_dir($dir), true) . "\n";
