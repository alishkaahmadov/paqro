<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DatabaseBackupController extends Controller
{
    public function backupDatabase()
    {
        set_time_limit(600);
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPassword = env('DB_PASSWORD');
        $host = env('DB_HOST');
        $port = env('DB_PORT');
        $backupFile = 'backup_' . date('Y_m_d_H_i_s') . '.sql';


        $process = new Process([
            'pg_dump',
            '-h', $host,
            '-U', $dbUser,
            '-d', $dbName,
            '-p', $port,
            '-F', 'c', // Custom format for smaller size
            '-f', storage_path('app/backups/' . $backupFile)
        ]);
        
        $process->setEnv(['PGPASSWORD' => $dbPassword]);

        try {
            $process->mustRun();
            return response()->json(['success' => true, 'message' => 'Backup created successfully']);
        } catch (ProcessFailedException $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
}
