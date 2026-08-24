<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

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

        } catch (\Exception $e) {
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
            // MySQL restore using mysql CLI
            $host = $config['host'] ?? 'localhost';
            $database = $config['database'];
            $user = $config['username'];
            $password = $config['password'] ?? '';
            $port = $config['port'] ?? 3306;

            // Try common mysql locations for XAMPP
            $mysqlPaths = [
                'C:\\xampp\\mysql\\bin\\mysql.exe',
                'C:\\xampp\\MariaDB\\bin\\mysql.exe',
                'mysql', // System PATH
            ];

            $mysql = null;
            foreach ($mysqlPaths as $path) {
                if (file_exists($path) || $path === 'mysql') {
                    $mysql = $path;
                    break;
                }
            }

            if (!$mysql) {
                throw new \Exception('mysql client not found');
            }

            // Build mysql command
            $cmd = escapeshellarg($mysql);
            $cmd .= ' --host=' . escapeshellarg($host);
            $cmd .= ' --port=' . escapeshellarg((string)$port);
            $cmd .= ' --user=' . escapeshellarg($user);
            
            // Only add password if it exists
            if (!empty($password)) {
                $cmd .= ' --password=' . escapeshellarg($password);
            }
            
            $cmd .= ' ' . escapeshellarg($database);
            $cmd .= ' < ' . escapeshellarg($sqlFile);
            
            // Execute command
            $output = [];
            $returnVar = 0;
            exec($cmd, $output, $returnVar);

            if ($returnVar === 0) {
                $this->info("MySQL database restored successfully.");
            } else {
                throw new \Exception('MySQL restore failed: ' . implode("\n", $output));
            }
        }
    }
}
