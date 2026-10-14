<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Traits\LogsUserActivity;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\PositionRate;
use App\Models\Setting;
use Illuminate\Http\Request;
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

        $position->update($validated);

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
            Artisan::call('backup:run');
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
            'backup_file' => 'required|string',
        ]);

        try {
            $backupPath = storage_path('backups');
            $backup = basename($validated['backup_file']);
            $backupFilePath = $backupPath . '/' . $backup;

            // Security check: ensure backup exists and is within backups directory
            if (!file_exists($backupFilePath)) {
                return redirect()->route('configuration.restore-form')->with('error', 'Backup not found.');
            }

            $realPath = realpath($backupFilePath);
            $realBackupPath = realpath($backupPath);
            if ($realPath === false || strpos($realPath, $realBackupPath) !== 0) {
                return redirect()->route('configuration.restore-form')->with('error', 'Invalid backup file.');
            }

            // Determine the database dump file location
            $dumpFile = null;
            
            if (is_dir($backupFilePath)) {
                // It's a directory backup, look for database_dump.sql inside
                $dumpFile = $backupFilePath . '/database_dump.sql';
            } elseif (is_file($backupFilePath) && strpos($backupFilePath, '.sql') !== false) {
                // It's a SQL file directly
                $dumpFile = $backupFilePath;
            }

            if (!$dumpFile || !file_exists($dumpFile)) {
                return redirect()->route('configuration.restore-form')->with('error', 'No database dump found in backup.');
            }

            // Restore the database using the backup:restore command
            Artisan::call('backup:restore', ['--source' => $dumpFile]);
            
            $this->logActivity('updated', 'configuration');
            return redirect()->route('configuration.index')->with('success', 'Database restored successfully!');
        } catch (\Exception $e) {
            return redirect()->route('configuration.restore-form')->with('error', 'Restore failed: ' . $e->getMessage());
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

            $zipFilename = $dirName . '_' . now()->format('Y-m-d_H-i-s') . '.zip';
            $zipPath = $tempDir . '/' . $zipFilename;

            // Try using PowerShell Compress-Archive on Windows
            if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                $this->createZipUsingPowerShell($dirPath, $zipPath);
            } else {
                // On Linux, try using system zip command
                $this->createZipUsingSystemZip($dirPath, $zipPath);
            }

            if (!file_exists($zipPath)) {
                throw new \Exception('Failed to create backup archive');
            }

            // Download and delete after sending
            return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->route('configuration.backup-history')
                ->with('error', 'Failed to prepare backup for download: ' . $e->getMessage());
        }
    }

    /**
     * Create ZIP using PowerShell (Windows)
     */
    private function createZipUsingPowerShell($source, $destination)
    {
        $source = str_replace('/', '\\', realpath($source));
        $destination = str_replace('/', '\\', $destination);
        
        $command = "powershell.exe -NoProfile -Command \"Add-Type -AssemblyName 'System.IO.Compression.FileSystem'; " .
                   "[System.IO.Compression.ZipFile]::CreateFromDirectory('" . addslashes($source) . "', '" . addslashes($destination) . "')\"";
        
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0) {
            throw new \Exception('PowerShell failed to create ZIP archive');
        }
    }

    /**
     * Create ZIP using system zip command (Linux/Mac)
     */
    private function createZipUsingSystemZip($source, $destination)
    {
        $parentDir = dirname($source);
        $dirName = basename($source);
        
        $command = "cd " . escapeshellarg($parentDir) . " && zip -r " . 
                   escapeshellarg($destination) . " " . escapeshellarg($dirName);
        
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0) {
            throw new \Exception('System zip command failed');
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
