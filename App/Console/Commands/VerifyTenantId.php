<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerifyTenantId extends Command
{
    protected $signature = 'tenant:verify-id {tenant}';
    protected $description = 'Verify the tenant ID being used';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        $this->info("Verifying tenant ID: {$tenantId}");
        
        // Log the tenant ID
        Log::info("Verifying tenant ID", [
            'tenant_id' => $tenantId,
            'tenant_function' => tenant('id'),
            'tenant_object' => tenant()
        ]);
        
        $this->info("Tenant ID verification complete. Check the logs for details.");
        
        return 0;
    }
} 