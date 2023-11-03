<?php

namespace App\Http\Controllers\DatabaseBackup;

use App\Repositories\DatabaseBackup\DatabaseBackupInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;


class DatabaseBackupController extends Controller
{

    protected $backupRepository;

    public function __construct(DatabaseBackupInterface $backupRepository)
    {
        $this->backupRepository = $backupRepository;
    }


    public function backup()
    {
        // Run the backup Artisan command
        Artisan::call('backup:run');

        return "ok";

        // // Get the output from the command, which should include the backup file path
        // $output = Artisan::output();
        // preg_match('/Backup stored at path (\S+)/', $output, $matches);

        // if (isset($matches[1])) {
        //     $backupPath = $matches[1];

        //    
        //     $generalDatabaseBackup = new \App\Models\DatabaseBackup();
        //     $generalDatabaseBackup->path = $backupPath;
        //     $generalDatabaseBackup->save();

        //     return response()->download($backupPath);
        // }

        // return response()->json(['error' => 'Backup failed.'], 500);
    }

}
