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
        Schema::create('tenant_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('tenant_id');
            $table->unsignedBigInteger('tenant_admin_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            // Foreign key to tenant_admins table
            $table->foreign('tenant_admin_id')
                  ->references('id')
                  ->on('tenant_admins')
                  ->onDelete('cascade');
                  
            // Foreign key to tenants table
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_credentials');
    }
};
