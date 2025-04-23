<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TenantDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptTenantDatabasePasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:encrypt-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypts existing tenant database passwords';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting encryption of tenant database passwords...");
        
        // Get all tenant databases
        $tenantDBs = TenantDatabase::all();
        
        $this->info("Found " . $tenantDBs->count() . " tenant database records.");
        
        $encrypted = 0;
        $skipped = 0;
        
        foreach ($tenantDBs as $tenantDB) {
            try {
                // Check if the password is already encrypted
                try {
                    Crypt::decryptString($tenantDB->getRawOriginal('database_password'));
                    // If we get here, it's already encrypted
                    $this->line("Password for tenant {$tenantDB->tenant_id} is already encrypted. Skipping.");
                    $skipped++;
                    continue;
                } catch (\Exception $e) {
                    // Password is not encrypted, proceed to encrypt it
                    $plainPassword = $tenantDB->getRawOriginal('database_password');
                    
                    if (empty($plainPassword)) {
                        $this->warn("Empty password for tenant {$tenantDB->tenant_id}. Skipping.");
                        $skipped++;
                        continue;
                    }
                    
                    // Get the raw value to bypass the accessor
                    $encryptedPassword = Crypt::encryptString($plainPassword);
                    
                    // Update directly in the database to bypass the mutator (which would double-encrypt)
                    DB::table('tenant_databases')
                        ->where('id', $tenantDB->id)
                        ->update(['database_password' => $encryptedPassword]);
                    
                    $this->info("Encrypted password for tenant {$tenantDB->tenant_id}");
                    $encrypted++;
                }
            } catch (\Exception $e) {
                $this->error("Error processing tenant {$tenantDB->tenant_id}: " . $e->getMessage());
            }
        }
        
        $this->info("Encryption complete. Encrypted: {$encrypted}, Skipped: {$skipped}");
        
        return Command::SUCCESS;
    }
} 