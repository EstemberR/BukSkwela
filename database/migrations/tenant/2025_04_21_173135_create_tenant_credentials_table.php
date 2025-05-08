<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only create this table if we're in a tenant database context
        $connection = Schema::connection($this->getConnection())->getConnection();
        $isTenantConnection = $connection->getName() === 'tenant' || 
            strpos($connection->getDatabaseName(), 'tenant_') === 0;
                
        if ($isTenantConnection) {
            // Create tenant_user_credentials table to store authentication
            // credentials in each tenant's own database
            Schema::create('tenant_user_credentials', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('password');
                $table->enum('user_type', ['admin', 'staff', 'student'])->default('admin');
                $table->unsignedBigInteger('user_id')->nullable(); // To link to the specific user record
                $table->boolean('is_active')->default(true);
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop this table if we're in a tenant database context
        $connection = Schema::connection($this->getConnection())->getConnection();
        $isTenantConnection = $connection->getName() === 'tenant' || 
            strpos($connection->getDatabaseName(), 'tenant_') === 0;
            
        if ($isTenantConnection) {
            Schema::dropIfExists('tenant_user_credentials');
        }
    }
};
