<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ReadLogs extends Command
{
    protected $signature = 'logs:read';
    protected $description = 'Read the latest logs';

    public function handle()
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            $this->error("Log file not found at: {$logPath}");
            return 1;
        }
        
        $logs = File::get($logPath);
        $this->info($logs);
        
        return 0;
    }
} 