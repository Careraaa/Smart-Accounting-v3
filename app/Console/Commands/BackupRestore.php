<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BackupRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:restore {--source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore application from a backup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $source = $this->option('source');

            if (!$source) {
                $this->error('Source backup file not specified.');
                return self::FAILURE;
            }

            // Handle both full path and filename 
            $backupFile = $source;
            if (!file_exists($backupFile)) {
                $backupPath = storage_path('backups');
                $backupFile = $backupPath . '/' . basename($source);
            }

            if (!file_exists($backupFile)) {
                $this->error("Backup file not found: {$backupFile}");
                return self::FAILURE;
            }

            // Check if it's a database dump or directory backup
            if (str_ends_with($backupFile, '.sql')) {
                $this->restoreDatabase($backupFile);
            } else {
                $this->error('Invalid backup file. Must be a .sql file.');
                return self::FAILURE;
            }

            $this->info("✓ Restore process completed!");
            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error("Restore failed: " . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Restore database from SQL dump
     */
    private function restoreDatabase($sqlFile)
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        if ($connection === 'sqlite') {
            // SQLite restore
            $dbPath = $config['database'] ?? database_path('database.sqlite');
            if (copy($sqlFile, $dbPath)) {
                $this->info("SQLite database restored.");
            } else {
                throw new \Exception('Failed to restore SQLite database');
            }
        } elseif ($connection === 'mysql') {
            $handle = fopen($sqlFile, 'rb');
            if ($handle === false) {
                throw new RuntimeException("Unable to read database backup [{$sqlFile}].");
            }

            $database = DB::connection();
            $database->unprepared('SET FOREIGN_KEY_CHECKS=0');

            try {
                foreach ($this->sqlStatements($handle) as $statement) {
                    $database->unprepared($statement);
                }
            } finally {
                fclose($handle);
                $database->unprepared('SET FOREIGN_KEY_CHECKS=1');
            }

            $this->info('MySQL database restored successfully.');
        } else {
            throw new RuntimeException("Database restore is not supported for the [{$connection}] connection.");
        }
    }

    /**
     * Read SQL statements without loading a potentially large dump into memory.
     */
    private function sqlStatements($handle): \Generator
    {
        $statement = '';
        $quote = null;
        $lineComment = false;
        $blockComment = false;

        while (($line = fgets($handle)) !== false) {
            $length = strlen($line);

            for ($i = 0; $i < $length; $i++) {
                $character = $line[$i];
                $next = $line[$i + 1] ?? '';

                if ($lineComment) {
                    if ($character === "\n") {
                        $lineComment = false;
                        $statement .= "\n";
                    }
                    continue;
                }

                if ($blockComment) {
                    if ($character === '*' && $next === '/') {
                        $blockComment = false;
                        $statement .= ' ';
                        $i++;
                    }
                    continue;
                }

                if ($quote !== null) {
                    $statement .= $character;

                    if ($character === '\\' && $quote !== '`' && $i + 1 < $length) {
                        $statement .= $line[++$i];
                    } elseif ($character === $quote) {
                        if ($next === $quote) {
                            $statement .= $line[++$i];
                        } else {
                            $quote = null;
                        }
                    }
                    continue;
                }

                if (($character === '-' && $next === '-' && (($line[$i + 2] ?? '') === '' || ord($line[$i + 2]) <= 32))
                    || $character === '#') {
                    $lineComment = true;
                    $i += $character === '-' ? 1 : 0;
                    continue;
                }

                if ($character === '/' && $next === '*' && ($line[$i + 2] ?? '') !== '!') {
                    $blockComment = true;
                    $i++;
                    continue;
                }

                if ($character === "'" || $character === '"' || $character === '`') {
                    $quote = $character;
                    $statement .= $character;
                    continue;
                }

                if ($character === ';') {
                    $statement = trim($statement);
                    if ($statement !== '') {
                        yield $statement;
                    }
                    $statement = '';
                    continue;
                }

                $statement .= $character;
            }
        }

        if ($quote !== null || $blockComment) {
            throw new RuntimeException('The SQL backup ended unexpectedly inside a quoted value or comment.');
        }

        $statement = trim($statement);
        if ($statement !== '') {
            yield $statement;
        }
    }
}
