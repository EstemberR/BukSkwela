<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DirectTableCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check-tables {database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Directly check tables in a database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $database = $this->argument('database');
        $this->info("Checking tables in database: {$database}");
        
        try {
            $query = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = '{$database}'";
            $tables = DB::select($query);
            
            if (empty($tables)) {
                $this->info("No tables found in database {$database}");
                return Command::SUCCESS;
            }
            
            $tableList = [];
            foreach ($tables as $table) {
                $tableList[] = [$table->TABLE_NAME];
            }
            
            $this->info("Tables in database {$database}:");
            $this->table(['Table Name'], $tableList);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error checking tables: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 