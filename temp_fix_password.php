<?php

// Define your password here - use the password you want to set
$newPassword = 'NewPassword123';
$tenantId = 'informationtechlogy'; // Correct tenant ID from the query
$email = 'jorellabeciatnt@gmail.com'; // Correct email from the query

// Get the tenant admin
$user = \App\Models\TenantAdmin::where('tenant_id', $tenantId)
    ->where('email', $email)
    ->first();

if (!$user) {
    echo "User not found with email {$email} in tenant {$tenantId}\n";
    return;
}

echo "Found user: {$user->email} (ID: {$user->id})\n";

// Hash the new password
$hashedPassword = \Illuminate\Support\Facades\Hash::make($newPassword);

// Update the user's password
$user->password = $hashedPassword;
$user->save();

echo "Updated password in tenant_admins table\n";

// Create tenant_user_credentials table if it doesn't exist
try {
    \Illuminate\Support\Facades\DB::connection('tenant')->statement('
        CREATE TABLE IF NOT EXISTS tenant_user_credentials (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
            email VARCHAR(255), 
            password VARCHAR(255), 
            tenant_id VARCHAR(255), 
            tenant_admin_id BIGINT UNSIGNED, 
            remember_token VARCHAR(100) NULL, 
            created_at TIMESTAMP NULL, 
            updated_at TIMESTAMP NULL
        )
    ');
    
    echo "Ensured tenant_user_credentials table exists\n";
    
    // Check if credential exists
    $credentialExists = \Illuminate\Support\Facades\DB::connection('tenant')
        ->table('tenant_user_credentials')
        ->where('tenant_admin_id', $user->id)
        ->exists();
    
    if ($credentialExists) {
        // Update existing credential
        \Illuminate\Support\Facades\DB::connection('tenant')
            ->table('tenant_user_credentials')
            ->where('tenant_admin_id', $user->id)
            ->update([
                'password' => $hashedPassword,
                'updated_at' => now()
            ]);
        
        echo "Updated existing credential in tenant_user_credentials table\n";
    } else {
        // Insert new credential
        \Illuminate\Support\Facades\DB::connection('tenant')
            ->table('tenant_user_credentials')
            ->insert([
                'email' => $user->email,
                'password' => $hashedPassword,
                'tenant_id' => $tenantId,
                'tenant_admin_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        
        echo "Inserted new credential in tenant_user_credentials table\n";
    }
    
    echo "Password has been set to: {$newPassword}\n";
    echo "You should now be able to log in with this password.\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 