<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Traits\LogsUserActivity;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\PositionRate;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SystemConfigurationController extends Controller
{
    use LogsUserActivity;
    public function index()
    {
        $this->logActivity('viewed', 'configuration');

        // Check custom maintenance mode
        $maintenanceFile = storage_path('maintenance.json');
        $maintenanceModeActive = file_exists($maintenanceFile);

        // Load system settings from database with fallbacks
        $settings = [
            'system_name' => Setting::get('system_name', config('app.name', 'Knights Transport')),
            'timezone' => Setting::get('timezone', config('app.timezone', 'UTC')),
            'date_format' => Setting::get('date_format', config('app.date_format', 'Y-m-d')),
            'language' => Setting::get('language', config('app.locale', 'en')),
            'backup_frequency' => Setting::get('backup_frequency', 'daily'),
            'backup_retention' => Setting::get('backup_retention', 30),
            'maintenance_mode' => $maintenanceModeActive,
            'testing_mode' => Setting::get('testing_mode', 'disabled'),
        ];

        // Get configured departments and positions
        $departments = Department::orderBy('name')->get();
        $positions = PositionRate::with('department')->orderBy('name')->get();

        return view('superadmin.configuration.index', compact('settings', 'departments', 'positions'));
    }

    /**
     * Store a new department.
     */
    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name',
        ]);

        $validated['is_active'] = true;

        Department::create($validated);

        $this->logActivity('created', "Department: {$validated['name']}", request()->url(), 'configuration');

        return redirect()->route('configuration.index', ['tab' => 'positions'])->with('success', 'Department created successfully!');
    }

    /**
     * Update a department.
     */
    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name,' . $department->id,
        ]);

        $validated['is_active'] = true;

        $department->update($validated);

        $this->logActivity('updated', "Department: {$department->name}", request()->url(), 'configuration', $department->id);

        return redirect()->route('configuration.index', ['tab' => 'positions'])->with('success', 'Department updated successfully!');
    }

    /**
     * Delete a department.
     */
    public function destroyDepartment(Department $department)
    {
        $this->logActivity('deleted', "Department: {$department->name}", request()->url(), 'configuration', $department->id);
        $department->delete();
        return redirect()->route('configuration.index', ['tab' => 'positions'])->with('success', 'Department deleted successfully!');
    }

    /**
     * Store a new position and daily rate.
     */
    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('position_rates')->where(fn ($query) => $query->where('department_id', $request->input('department_id'))),
            ],
            'daily_rate' => 'required|numeric|between:0,999999.99',
            'department_id' => 'required|exists:departments,id',
        ]);

        $validated['is_active'] = true;

        PositionRate::create($validated);

        $this->logActivity('created', "Position: {$validated['name']}", request()->url(), 'configuration');

        return redirect()->route('configuration.index', ['tab' => 'positions'])->with('success', 'Position created successfully!');
    }

    /**
     * Update a position and daily rate.
     */
    public function updatePosition(Request $request, PositionRate $position)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('position_rates')->where(fn ($query) => $query->where('department_id', $request->input('department_id')))->ignore($position->id),
            ],
            'daily_rate' => 'required|numeric|between:0,999999.99',
            'department_id' => 'required|exists:departments,id',
        ]);

        $validated['is_active'] = true;
        $previousName = $position->name;

        DB::transaction(function () use ($position, $validated, $previousName) {
            $position->update($validated);

            User::where('position', $previousName)
                ->update(['salary_rate' => $validated['daily_rate']]);
        });

        $this->logActivity('updated', "Position: {$position->name}", request()->url(), 'configuration', $position->id);

        return redirect()->route('configuration.index', ['tab' => 'positions'])->with('success', 'Position updated successfully!');
    }

    /**
     * Delete a position.
     */
    public function destroyPosition(PositionRate $position)
    {
        $this->logActivity('deleted', "Position: {$position->name}", request()->url(), 'configuration', $position->id);
        $position->delete();
        return redirect()->route('configuration.index', ['tab' => 'positions'])->with('success', 'Position deleted successfully!');
    }

    /**
     * Update General Settings
     */
    public function updateGeneralSettings(Request $request)
    {
        $validated = $request->validate([
            'system_name' => 'required|string|max:255',
            'timezone' => 'required|string|timezone',
            'date_format' => 'required|string|in:Y-m-d,d-m-Y,m-d-Y,Y/m/d,d/m/Y,m/d/Y',
            'language' => 'required|string|in:en,es,fr,de,tl,fil',
        ]);

        // Store settings (example using file storage or database)
        // You can extend this to use a settings table if needed
        $this->saveSettings($validated);

        $this->logActivity('updated', 'configuration');

        return redirect()->route('configuration.index')->with('success', 'General settings updated successfully!');
    }

    /**
     * Update Backup Settings
     */
    public function updateBackupSettings(Request $request)
    {
        $validated = $request->validate([
            'backup_frequency' => 'required|string|in:hourly,daily,weekly,monthly',
            'backup_retention' => 'required|integer|min:1|max:365',
        ]);

        $this->saveSettings($validated);

        $this->logActivity('updated', 'configuration');

        return redirect()->route('configuration.index')->with('success', 'Backup settings updated successfully!');
    }

    /**
     * Trigger backup now
     */
    public function backupNow()
    {
        try {
            $exitCode = Artisan::call('backup:run');
            if ($exitCode !== 0) {
                $output = trim(Artisan::output());
                return redirect()->route('configuration.index')
                    ->with('error', $output ?: 'Backup failed. Check the application logs for details.');
            }

            $this->logActivity('updated', 'configuration');
            return redirect()->route('configuration.index')->with('success', 'Backup created successfully!')->with('_sound_backup', true);
        } catch (\Exception $e) {
            return redirect()->route('configuration.index')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Display backup history
     */
    public function backupHistory()
    {
        $backups = [];
        
        $backupDestination = storage_path('backups');
        if (is_dir($backupDestination)) {
            $items = scandir($backupDestination, SCANDIR_SORT_DESCENDING);
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..') {
                    $itemPath = $backupDestination . '/' . $item;
                    
                    // Handle both directories and files
                    if (is_dir($itemPath)) {
                        $size = $this->getDirectorySize($itemPath);
                    } else {
                        $size = filesize($itemPath);
                    }
                    
                    $backups[] = [
                        'filename' => $item,
                        'size' => $size,
                        'created_at' => filemtime($itemPath),
                    ];
                }
            }
        }

        return view('superadmin.configuration.backup-history', compact('backups'));
    }

    /**
     * Get total size of a directory recursively
     */
    private function getDirectorySize($path)
    {
        $size = 0;
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $path . '/' . $file;
                    if (is_dir($filePath)) {
                        $size += $this->getDirectorySize($filePath);
                    } else {
                        $size += filesize($filePath);
                    }
                }
            }
        }
        return $size;
    }

    /**
     * Show restore database form
     */
    public function showRestoreForm()
    {
        $backups = [];
        $backupPath = storage_path('backups');
        
        if (is_dir($backupPath)) {
            $items = scandir($backupPath, SCANDIR_SORT_DESCENDING);
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..') {
                    $itemPath = $backupPath . '/' . $item;
                    
                    // Include both directories and SQL dump files
                    if (is_dir($itemPath) && is_file($itemPath . '/database_dump.sql')) {
                        $backups[] = [
                            'filename' => $item,
                            'type' => 'directory',
                            'hasDump' => true,
                        ];
                    } elseif (is_file($itemPath) && (strpos($item, '.sql') !== false)) {
                        $backups[] = [
                            'filename' => $item,
                            'type' => 'file',
                            'hasDump' => true,
                        ];
                    }
                }
            }
        }
        
        return view('superadmin.configuration.restore-database', compact('backups'));
    }

    /**
     * Restore database from backup
     */
    public function restoreDatabase(Request $request)
    {
        $validated = $request->validate([
            'backup_file' => 'nullable|required_without:backup_upload|string',
            'backup_upload' => 'nullable|required_without:backup_file|file|mimes:zip|extensions:zip|max:512000',
        ]);

        if ($request->filled('backup_file') && $request->hasFile('backup_upload')) {
            return redirect()->route('configuration.restore-form')
                ->withErrors(['backup_upload' => 'Choose a saved backup or upload a backup ZIP, not both.'])
                ->withInput();
        }

        $temporaryDump = null;
        try {
            if ($request->hasFile('backup_upload')) {
                $temporaryDump = $this->extractUploadedBackupDump($request->file('backup_upload')->getRealPath());
                $dumpFile = $temporaryDump;
            } else {
                $backupPath = storage_path('backups');
                $backup = basename($validated['backup_file']);
                $backupFilePath = $backupPath . '/' . $backup;

                if (!file_exists($backupFilePath)) {
                    return redirect()->route('configuration.restore-form')->with('error', 'Backup not found.');
                }

                $realPath = realpath($backupFilePath);
                $realBackupPath = realpath($backupPath);
                if ($realPath === false || $realBackupPath === false || strpos($realPath, $realBackupPath . DIRECTORY_SEPARATOR) !== 0) {
                    return redirect()->route('configuration.restore-form')->with('error', 'Invalid backup file.');
                }

                if (is_dir($backupFilePath)) {
                    $dumpFile = $backupFilePath . DIRECTORY_SEPARATOR . 'database_dump.sql';
                } elseif (is_file($backupFilePath) && str_ends_with(strtolower($backupFilePath), '.sql')) {
                    $dumpFile = $backupFilePath;
                } else {
                    $dumpFile = null;
                }

                if (!$dumpFile || !is_file($dumpFile)) {
                    return redirect()->route('configuration.restore-form')->with('error', 'No database dump found in backup.');
                }
            }

            // Restore the database using the backup:restore command
            $exitCode = Artisan::call('backup:restore', ['--source' => $dumpFile]);
            if ($exitCode !== 0) {
                $output = trim(Artisan::output());
                return redirect()->route('configuration.restore-form')
                    ->with('error', $output ?: 'Database restore failed. Check the application logs for details.');
            }
            
            $this->logActivity('updated', 'configuration');
            return redirect()->route('configuration.restore-form')->with('success', 'Database restored successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('configuration.restore-form')->with('error', 'Restore failed: ' . $e->getMessage());
        } finally {
            if ($temporaryDump !== null && is_file($temporaryDump)) {
                unlink($temporaryDump);
            }
        }
    }

    private function extractUploadedBackupDump(string $uploadedArchive): string
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('The PHP ZIP extension is required to restore uploaded backups.');
        }

        $archive = new \ZipArchive();
        if ($archive->open($uploadedArchive) !== true) {
            throw new \RuntimeException('The uploaded file is not a valid backup archive.');
        }

        $temporaryDump = null;
        try {
            $manifestContents = $archive->getFromName('backup_manifest.json');
            if ($manifestContents === false) {
                throw new \RuntimeException('This ZIP does not contain a supported backup verification manifest.');
            }

            $manifest = json_decode($manifestContents, true, 512, JSON_THROW_ON_ERROR);
            if (
                !is_array($manifest)
                || ($manifest['format'] ?? null) !== 'smart-accounting-backup'
                || !in_array($manifest['version'] ?? null, [1, 2], true)
                || !is_string($manifest['backup'] ?? null)
                || !preg_match('/^backup_[A-Za-z0-9_-]+$/', $manifest['backup'])
                || !is_string($manifest['database_sha256'] ?? null)
                || !preg_match('/^[a-f0-9]{64}$/', $manifest['database_sha256'])
                || (($manifest['version'] ?? null) === 1
                    && (!is_string($manifest['signature'] ?? null) || !preg_match('/^[a-f0-9]{64}$/', $manifest['signature'])))
            ) {
                throw new \RuntimeException('The backup archive has an invalid verification manifest.');
            }

            $entryIndex = $archive->locateName('database_dump.sql', \ZipArchive::FL_NODIR);
            if ($entryIndex === false) {
                throw new \RuntimeException('The backup archive does not contain a database dump.');
            }

            $entry = $archive->statIndex($entryIndex);
            if (!$entry || $entry['size'] < 1 || $entry['size'] > 536870912) {
                throw new \RuntimeException('The database dump is empty or exceeds the 512 MB restore limit.');
            }

            $tempDirectory = storage_path('temp');
            if (!is_dir($tempDirectory) && !mkdir($tempDirectory, 0755, true) && !is_dir($tempDirectory)) {
                throw new \RuntimeException('Unable to prepare temporary storage for the uploaded backup.');
            }

            $temporaryDump = tempnam($tempDirectory, 'restore-');
            if ($temporaryDump === false) {
                throw new \RuntimeException('Unable to create temporary storage for the database dump.');
            }

            $sqlDumpPath = $temporaryDump . '.sql';
            if (!rename($temporaryDump, $sqlDumpPath)) {
                throw new \RuntimeException('Unable to prepare the database dump for restoration.');
            }
            $temporaryDump = $sqlDumpPath;

            $input = $archive->getStream('database_dump.sql');
            $output = fopen($temporaryDump, 'wb');
            if ($input === false || $output === false) {
                if (is_resource($input)) {
                    fclose($input);
                }
                if (is_resource($output)) {
                    fclose($output);
                }
                throw new \RuntimeException('Unable to read the database dump from the backup archive.');
            }

            $hash = hash_init('sha256');
            try {
                while (!feof($input)) {
                    $chunk = fread($input, 8192);
                    if ($chunk === false) {
                        throw new \RuntimeException('Unable to read the database dump from the backup archive.');
                    }
                    if ($chunk === '') {
                        continue;
                    }

                    hash_update($hash, $chunk);
                    $offset = 0;
                    while ($offset < strlen($chunk)) {
                        $written = fwrite($output, substr($chunk, $offset));
                        if ($written === false || $written === 0) {
                            throw new \RuntimeException('Unable to extract the database dump from the backup archive.');
                        }
                        $offset += $written;
                    }
                }
            } finally {
                fclose($input);
                fclose($output);
            }
            $actualHash = hash_final($hash);
            if (!hash_equals($manifest['database_sha256'], $actualHash)) {
                throw new \RuntimeException('The backup archive verification failed. The file may be damaged or modified.');
            }

            return $temporaryDump;
        } catch (\Throwable $e) {
            if ($temporaryDump !== null && is_file($temporaryDump)) {
                unlink($temporaryDump);
            }
            throw $e;
        } finally {
            $archive->close();
        }
    }

    /**
     * Toggle maintenance mode (superadmin can still access)
     */
    public function toggleMaintenanceMode(Request $request)
    {
        try {
            $maintenanceFile = storage_path('maintenance.json');
            $isCurrentlyDown = file_exists($maintenanceFile);

            if ($isCurrentlyDown) {
                // Turn off maintenance mode
                unlink($maintenanceFile);
                $message = 'Maintenance mode disabled.';
            } else {
                // Turn on maintenance mode
                $maintenanceData = [
                    'time' => time(),
                    'enabled' => true,
                ];
                file_put_contents($maintenanceFile, json_encode($maintenanceData));
                $message = 'Maintenance mode enabled. Superadmin users can still access the system.';
            }

            $this->logActivity('updated', 'configuration');
            return redirect()->route('configuration.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('configuration.index')->with('error', 'Failed to toggle maintenance mode: ' . $e->getMessage());
        }
    }

    /**
     * Toggle testing mode (quick-add attendance in calendar views)
     */
    public function toggleTestingMode()
    {
        try {
            $current = Setting::get('testing_mode', 'disabled');
            $enabling = $current !== 'enabled';
            Setting::set('testing_mode', $enabling ? 'enabled' : 'disabled');
            $this->logActivity('updated', 'configuration');
            $flash = $enabling ? ['success' => 'Testing mode enabled.', '_sound_testing' => true] : ['success' => 'Testing mode disabled.'];
            return redirect()->route('configuration.index')->with($flash);
        } catch (\Exception $e) {
            return redirect()->route('configuration.index')->with('error', 'Failed to toggle testing mode: ' . $e->getMessage());
        }
    }

    /**
     * Clear system cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            $this->logActivity('updated', 'configuration');
            
            return redirect()->route('configuration.index')->with('success', 'System cache cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->route('configuration.index')->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear system logs
     */
    public function clearLogs()
    {
        try {
            $logPath = storage_path('logs');
            $files = glob($logPath . '/*.log');
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            $this->logActivity('updated', 'configuration');

            return redirect()->route('configuration.index')->with('success', 'System logs cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->route('configuration.index')->with('error', 'Failed to clear logs: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Save settings to configuration file
     */
    private function saveSettings(array $settings)
    {
        // Save settings to database for persistence
        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }

    /**
     * Download a backup
     */
    public function downloadBackup($filename)
    {
        try {
            $backupPath = storage_path('backups');
            $sanitizedFilename = basename($filename);
            $filePath = $backupPath . '/' . $sanitizedFilename;

            // Security check: ensure path exists (file or directory)
            if (!file_exists($filePath)) {
                return redirect()->route('configuration.backup-history')->with('error', 'Backup file not found.');
            }

            // Verify the path is within backups directory (prevent directory traversal)
            $realPath = realpath($filePath);
            $realBackupPath = realpath($backupPath);
            if ($realPath === false || strpos($realPath, $realBackupPath) !== 0) {
                return redirect()->route('configuration.backup-history')->with('error', 'Invalid backup file.');
            }

            // For directories, create a zip and download
            if (is_dir($filePath)) {
                return $this->downloadBackupDirectoryAsZip($filePath, $sanitizedFilename);
            }

            // Download the file
            return response()->download($filePath, $sanitizedFilename);

        } catch (\Exception $e) {
            return redirect()->route('configuration.backup-history')->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete a backup
     */
    public function deleteBackup($filename)
    {
        try {
            $backupPath = storage_path('backups');
            $sanitizedFilename = basename($filename);
            $filePath = $backupPath . '/' . $sanitizedFilename;

            // Security check: ensure path exists
            if (!file_exists($filePath)) {
                return redirect()->route('configuration.backup-history')->with('error', 'Backup not found.');
            }

            // Verify the path is within backups directory (prevent directory traversal)
            $realPath = realpath($filePath);
            $realBackupPath = realpath($backupPath);
            if ($realPath === false || strpos($realPath, $realBackupPath) !== 0) {
                return redirect()->route('configuration.backup-history')->with('error', 'Invalid backup file.');
            }

            // Delete the backup (file or directory)
            if (is_dir($filePath)) {
                $this->recursiveDelete($filePath);
            } else {
                unlink($filePath);
            }

            $this->logActivity('updated', 'configuration');
            return redirect()->route('configuration.backup-history')->with('success', 'Backup deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->route('configuration.backup-history')->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    /**
     * Recursively delete a directory and its contents
     */
    private function recursiveDelete($path)
    {
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $path . '/' . $file;
                    if (is_dir($filePath)) {
                        $this->recursiveDelete($filePath);
                    } else {
                        unlink($filePath);
                    }
                }
            }
            rmdir($path);
        } else {
            unlink($path);
        }
    }

    /**
     * Download a backup directory as a zip file
     */
    private function downloadBackupDirectoryAsZip($dirPath, $dirName)
    {
        try {
            $tempDir = storage_path('temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $zipFilename = $dirName . '_' . now()->format('Y-m-d_H-i-s') . '_' . bin2hex(random_bytes(4)) . '.zip';
            $zipPath = $tempDir . '/' . $zipFilename;

            $this->createZipArchive($dirPath, $zipPath);

            // Download and delete after sending
            return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->route('configuration.backup-history')
                ->with('error', 'Failed to prepare backup for download: ' . $e->getMessage());
        }
    }

    private function createZipArchive(string $source, string $destination): void
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('The PHP ZIP extension is required to download backup folders.');
        }

        $sourcePath = realpath($source);
        if ($sourcePath === false || !is_dir($sourcePath)) {
            throw new \RuntimeException('The backup folder could not be found.');
        }

        $archive = new \ZipArchive();
        $result = $archive->open($destination, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($result !== true) {
            throw new \RuntimeException("Unable to create backup archive (ZIP error {$result}).");
        }

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourcePath, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                if ($item->isLink()) {
                    continue;
                }

                $relativePath = substr($item->getPathname(), strlen($sourcePath) + 1);
                $archivePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

                if ($item->isDir()) {
                    if (!$archive->addEmptyDir($archivePath)) {
                        throw new \RuntimeException("Unable to add folder [{$archivePath}] to the backup archive.");
                    }
                } elseif (!$archive->addFile($item->getPathname(), $archivePath)) {
                    throw new \RuntimeException("Unable to add file [{$archivePath}] to the backup archive.");
                }
            }

            $databaseDump = $sourcePath . DIRECTORY_SEPARATOR . 'database_dump.sql';
            if (is_file($databaseDump)) {
                $databaseHash = hash_file('sha256', $databaseDump);
                if ($databaseHash === false) {
                    throw new \RuntimeException('Unable to verify the database dump for this backup.');
                }

                $manifest = [
                    'format' => 'smart-accounting-backup',
                    'version' => 2,
                    'backup' => basename($sourcePath),
                    'database_sha256' => $databaseHash,
                ];

                $manifestContents = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                if ($manifestContents === false || !$archive->addFromString('backup_manifest.json', $manifestContents . "\n")) {
                    throw new \RuntimeException('Unable to add backup verification data to the archive.');
                }
            }

        } catch (\Throwable $e) {
            $archive->close();
            if (file_exists($destination)) {
                unlink($destination);
            }
            throw $e;
        }

        if (!$archive->close()) {
            if (file_exists($destination)) {
                unlink($destination);
            }
            throw new \RuntimeException('Unable to finish creating the backup archive.');
        }

        if (!is_file($destination) || filesize($destination) === 0) {
            throw new \RuntimeException('The backup archive was created empty.');
        }
    }

    /**
     * Helper: Get backup files
     */
    private function getBackupFiles()
    {
        $backups = [];
        $backupPath = storage_path('backups');
        
        if (is_dir($backupPath)) {
            $files = array_diff(scandir($backupPath), ['.', '..']);
            foreach ($files as $file) {
                if (strpos($file, '.zip') !== false || strpos($file, '.tar') !== false) {
                    $backups[] = $file;
                }
            }
        }

        return array_reverse($backups); // Most recent first
    }
}
