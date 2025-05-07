<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckMySQLConnections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:check-mysql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check MySQL connection limits and current usage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Get MySQL max_connections setting
            $maxConnections = DB::select("SHOW VARIABLES LIKE 'max_connections'");
            $maxConnValue = $maxConnections[0]->Value;
            
            // Get current connection count
            $currentConnections = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            $currentConnValue = $currentConnections[0]->Value;
            
            // Calculate percentage used
            $percentUsed = ($currentConnValue / $maxConnValue) * 100;
            
            // Get max used connections since server start
            $maxUsedConnections = DB::select("SHOW STATUS LIKE 'Max_used_connections'");
            $maxUsedValue = $maxUsedConnections[0]->Value;
            $maxPercentEverUsed = ($maxUsedValue / $maxConnValue) * 100;
            
            $this->info("MySQL Connection Information:");
            $this->info("-----------------------------");
            $this->info("Maximum allowed connections: $maxConnValue");
            $this->info("Current connections: $currentConnValue ($percentUsed%)");
            $this->info("Peak connections since server start: $maxUsedValue ($maxPercentEverUsed%)");
            
            // Warning level
            if ($percentUsed > 80) {
                $this->error("WARNING: MySQL connection usage is very high at $percentUsed%!");
                $this->error("Running tenant migrations may fail due to connection limits!");
                return Command::FAILURE;
            } elseif ($percentUsed > 60) {
                $this->warn("CAUTION: MySQL connection usage is moderate at $percentUsed%");
                $this->warn("Consider using batched migrations with small batch sizes.");
                return Command::SUCCESS;
            } else {
                $this->info("MySQL connection usage is good at $percentUsed%");
                $this->info("You should be able to run tenant migrations without issues.");
                return Command::SUCCESS;
            }
            
        } catch (\Exception $e) {
            $this->error("Error checking MySQL connections: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 