<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;
use RuntimeException;

class BackupRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run {--only-files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the application files and database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Starting backup process...');

            $backupPath = storage_path('backups');
            
            // Create backups directory if it doesn't exist
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $timestamp = now()->format('Y-M-d_H-i-s');

            // Option 1: Only files backup
            if ($this->option('only-files')) {
                $this->info('Creating files backup (files only)...');
                $this->backupFiles(null, $timestamp);
            } else {
                // Option 2: Full backup (files + database)
                $this->info('Creating database dump...');
                $this->backupDatabase($backupPath, $timestamp);
                
                $this->info('Creating files backup with database...');
                $this->backupFiles(null, $timestamp);
            }

            $this->info("✓ Backup completed successfully!");
            $this->info("Backup location: storage/backups/backup_{$timestamp}");
            
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Backup files using PHP's directory functions
     */
    private function backupFiles($backupFilePath, $timestamp = null)
    {
        if (!$timestamp) {
            $timestamp = now()->format('Y-M-d_H-i-s');
        }

        $backupPath = storage_path('backups');
        $dirBackupPath = $backupPath . "/backup_{$timestamp}";

        if (!is_dir($dirBackupPath)) {
            mkdir($dirBackupPath, 0755, true);
        }

        // Copy app files
        $this->recursiveCopy(app_path(), $dirBackupPath . '/app');
        
        // Copy config files
        $this->recursiveCopy(config_path(), $dirBackupPath . '/config');
        
        // Copy resources
        $this->recursiveCopy(resource_path(), $dirBackupPath . '/resources');
        
        // Copy routes
        $this->recursiveCopy(base_path('routes'), $dirBackupPath . '/routes');
        
        // Copy database migrations
        if (is_dir(database_path('migrations'))) {
            $this->recursiveCopy(database_path('migrations'), $dirBackupPath . '/database/migrations');
        }

        // Copy database dump if it exists
        $dumpFile = $backupPath . "/database_{$timestamp}.sql";
        if (file_exists($dumpFile)) {
            copy($dumpFile, $dirBackupPath . '/database_dump.sql');
            // Delete the separate dump file since it's now included
            unlink($dumpFile);
            $this->info('Database included in backup');
        }

        $this->info("Files backed up to: backup_{$timestamp}");
    }

    /**
     * Backup database
     */
    private function backupDatabase($backupPath, $timestamp)
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        $dumpFile = $backupPath . "/database_{$timestamp}.sql";

        if ($connection === 'sqlite') {
            // SQLite backup
            $dbPath = $config['database'] ?? database_path('database.sqlite');
            if (file_exists($dbPath)) {
                copy($dbPath, $dumpFile);
                $this->info("SQLite database backed up.");
            }
        } elseif ($connection === 'mysql') {
            $this->backupMySqlDatabase($dumpFile);
        } else {
            throw new RuntimeException("Database backups are not supported for the [{$connection}] connection.");
        }
    }

    /**
     * Export MySQL through the configured PDO connection, without requiring shell access.
     */
    private function backupMySqlDatabase(string $dumpFile): void
    {
        $pdo = DB::connection()->getPdo();
        $temporaryFile = $dumpFile . '.tmp';
        $handle = fopen($temporaryFile, 'wb');

        if ($handle === false) {
            throw new RuntimeException("Unable to create database backup at {$temporaryFile}.");
        }

        try {
            $this->writeDump($handle, "-- MySQL database backup\nSET FOREIGN_KEY_CHECKS=0;\n\n");
            $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                $quotedTable = $this->quoteIdentifier($table);
                $createTable = $pdo->query("SHOW CREATE TABLE {$quotedTable}")->fetch(PDO::FETCH_NUM);

                if (!$createTable || !isset($createTable[1])) {
                    throw new RuntimeException("Unable to read the definition for table [{$table}].");
                }

                $this->writeDump($handle, "DROP TABLE IF EXISTS {$quotedTable};\n{$createTable[1]};\n\n");

                $columnTypes = [];
                $columns = $pdo->query("SHOW COLUMNS FROM {$quotedTable}")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($columns as $column) {
                    $columnTypes[$column['Field']] = $column['Type'];
                }

                $rows = $pdo->query("SELECT * FROM {$quotedTable}");
                while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
                    $columnNames = array_keys($row);
                    $quotedColumns = implode(', ', array_map(
                        fn (string $column): string => $this->quoteIdentifier($column),
                        $columnNames
                    ));
                    $values = [];

                    foreach ($row as $column => $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } elseif (preg_match('/^(tinyblob|blob|mediumblob|longblob|binary|varbinary|bit)(\(|$)/i', $columnTypes[$column] ?? '')) {
                            $values[] = '0x' . bin2hex((string) $value);
                        } else {
                            $quotedValue = $pdo->quote((string) $value);
                            if ($quotedValue === false) {
                                throw new RuntimeException("Unable to encode a value from table [{$table}].");
                            }
                            $values[] = $quotedValue;
                        }
                    }

                    $this->writeDump(
                        $handle,
                        "INSERT INTO {$quotedTable} ({$quotedColumns}) VALUES (" . implode(', ', $values) . ");\n"
                    );
                }

                $this->writeDump($handle, "\n");
            }

            $this->writeDump($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            if (!fflush($handle)) {
                throw new RuntimeException('Unable to finish writing the MySQL database backup.');
            }
        } catch (\Throwable $e) {
            fclose($handle);
            unlink($temporaryFile);
            throw $e;
        }

        fclose($handle);

        if (!rename($temporaryFile, $dumpFile)) {
            unlink($temporaryFile);
            throw new RuntimeException("Unable to finalize database backup at {$dumpFile}.");
        }

        $this->info('MySQL database backed up (' . $this->formatBytes(filesize($dumpFile)) . ').');
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function writeDump($handle, string $contents): void
    {
        $length = strlen($contents);
        $written = 0;

        while ($written < $length) {
            $result = fwrite($handle, substr($contents, $written));
            if ($result === false || $result === 0) {
                throw new RuntimeException('Unable to write the MySQL database backup.');
            }
            $written += $result;
        }
    }

    /**
     * Format bytes to human readable size
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Recursively copy directories
     */
    private function recursiveCopy($src, $dst)
    {
        if (!is_dir($src)) {
            return;
        }

        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }

        $dir = opendir($src);
        while (($file = readdir($dir)) !== false) {
            if ($file !== '.' && $file !== '..' && $file !== '.gitkeep') {
                $srcPath = $src . '/' . $file;
                $dstPath = $dst . '/' . $file;

                if (is_dir($srcPath)) {
                    $this->recursiveCopy($srcPath, $dstPath);
                } else {
                    copy($srcPath, $dstPath);
                }
            }
        }
        closedir($dir);
    }
}
