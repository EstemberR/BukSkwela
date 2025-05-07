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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('tenant_name')->nullable()->after('id');
            $table->string('tenant_email')->nullable()->after('tenant_name');
            $table->enum('status', ['pending', 'approved', 'rejected', 'disabled', 'denied'])->default('pending')->after('tenant_email');
            $table->string('subscription_plan')->default('basic')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['tenant_name', 'tenant_email', 'status', 'subscription_plan']);
        });
    }
};