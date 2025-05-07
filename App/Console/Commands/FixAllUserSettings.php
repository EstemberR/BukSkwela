<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UserSettingsCreator;
use App\Models\TenantAdmin;
use Illuminate\Support\Facades\Log;

class FixAllUserSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:fix-all-settings {--tenant= : Only fix settings for a specific tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or fix settings for all existing users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting user settings creation/update...');
        
        $settingsCreator = new UserSettingsCreator();
        $tenantId = $this->option('tenant');
        
        if ($tenantId) {
            $this->info("Fixing settings for tenant: {$tenantId}");
            $results = $settingsCreator->createForTenant($tenantId);
            
            $this->info("Created/updated settings for " . count($results) . " users in tenant {$tenantId}");
            
            foreach ($results as $result) {
                $this->line("  - Created settings for user ID: {$result['user_id']}");
            }
        } else {
            $this->info("Fixing settings for all tenants...");
            $results = $settingsCreator->createForAllTenants();
            
            $totalUsers = 0;
            foreach ($results as $tenantId => $tenantResults) {
                $totalUsers += count($tenantResults);
                $this->info("  - Tenant {$tenantId}: " . count($tenantResults) . " users updated");
            }
            
            $this->info("Completed! Created/updated settings for {$totalUsers} users across all tenants");
        }
        
        return 0;
    }
} 