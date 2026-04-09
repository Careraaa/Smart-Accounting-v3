<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

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

            $timestamp = now()->format('Y-m-d_H-i-s');

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
            $timestamp = now()->format('Y-m-d_H-i-s');
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
            // MySQL backup using mysqldump
            $host = $config['host'] ?? 'localhost';
            $database = $config['database'];
            $user = $config['username'];
            $password = $config['password'] ?? '';
            $port = $config['port'] ?? 3306;

            // Try common mysqldump locations for XAMPP
            $mysqldumpPaths = [
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                'C:\\xampp\\MariaDB\\bin\\mysqldump.exe',
                'mysqldump', // System PATH
            ];

            $mysqldump = null;
            foreach ($mysqldumpPaths as $path) {
                if (file_exists($path) || $path === 'mysqldump') {
                    $mysqldump = $path;
                    break;
                }
            }

            if ($mysqldump) {
                // Build mysqldump command
                $cmd = escapeshellarg($mysqldump);
                $cmd .= ' --host=' . escapeshellarg($host);
                $cmd .= ' --port=' . escapeshellarg((string)$port);
                $cmd .= ' --user=' . escapeshellarg($user);
                
                // Only add password if it exists
                if (!empty($password)) {
                    $cmd .= ' --password=' . escapeshellarg($password);
                }
                
                $cmd .= ' ' . escapeshellarg($database);
                $cmd .= ' > ' . escapeshellarg($dumpFile);
                
                // Execute command
                $output = [];
                $returnVar = 0;
                exec($cmd, $output, $returnVar);

                if ($returnVar === 0 && file_exists($dumpFile) && filesize($dumpFile) > 0) {
                    $size = filesize($dumpFile);
                    $this->info("MySQL database backed up (" . $this->formatBytes($size) . ").");
                } else {
                    $this->warn("MySQL database backup encountered issues.");
                }
            } else {
                $this->warn("mysqldump not found. Database not backed up.");
            }
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
