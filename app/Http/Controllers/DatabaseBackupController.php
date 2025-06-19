<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use ZipArchive;
use Illuminate\Support\Facades\File;

class DatabaseBackupController extends Controller
{
    public function backupDatabase()
    {
        // *** 6 Backup
        set_time_limit(600);

        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPassword = env('DB_PASSWORD');
        $host = env('DB_HOST');
        $port = env('DB_PORT');
        
        // Define backup filenames
        $dbBackupFile = 'backup_' . date('Y_m_d_H_i_s') . '.sql';
        $fileBackupFile = 'public_backup_' . date('Y_m_d_H_i_s') . '.tar.gz';

        // Paths
        $dbBackupPath = storage_path('app/backups/' . $dbBackupFile);
        $fileBackupPath = storage_path('app/backups/' . $fileBackupFile);
        $zipPath = storage_path('app/backups/backup_' . date('Y_m_d_H_i_s') . '.zip');

        // Backup Database
        $processDb = new Process([
            'pg_dump',
            '-h', $host,
            '-U', $dbUser,
            '-d', $dbName,
            '-p', $port,
            '-F', 'p', // Plain format
            '-f', $dbBackupPath,
        ]);

        $processDb->setEnv(['PGPASSWORD' => $dbPassword]);
        $processDb->setTimeout(600);

        // Backup the public directory (tar.gz format)
        $processFiles = new Process([
            'tar',
            '-czf', $fileBackupPath,
            '-C', storage_path('app'),
            'public',
        ]);

        $processFiles->setTimeout(600);

        try {
            // Run the backup processes
            $processDb->mustRun();
            $processFiles->mustRun();

            // Create a ZIP file containing both backups
            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
                $zip->addFile($dbBackupPath, $dbBackupFile);
                $zip->addFile($fileBackupPath, $fileBackupFile);
                $zip->close();
            }

            // Delete the original files after zipping
            File::delete([$dbBackupPath, $fileBackupPath]);

            // Return the ZIP file for download
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (ProcessFailedException $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
}
