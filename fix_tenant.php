<?php

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Get tenant ID from command line or default to 'nursing'
$tenantId = $argv[1] ?? 'nursing';

echo "Fixing tenant database credentials for {$tenantId}...\n";

// Update tenant database credentials to use root
$result = DB::table('tenant_databases')
    ->where('tenant_id', $tenantId)
    ->update([
        'database_username' => 'root',
        'database_password' => ''
    ]);

if ($result) {
    echo "Updated tenant database credentials to use root account\n";
} else {
    echo "Failed to update credentials - record may not exist\n";
}

// Clear config cache
echo "Clearing cache...\n";
$kernel->call('config:clear');
$kernel->call('cache:clear');

echo "Done. Please try accessing the tenant site again.\n";