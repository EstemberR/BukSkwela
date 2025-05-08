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
        Schema::table('tenant_databases', function (Blueprint $table) {
            // Modify column type from string to text to accommodate encrypted passwords
            $table->text('database_password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_databases', function (Blueprint $table) {
            // Revert back to string type
            $table->string('database_password')->nullable()->change();
        });
    }
}; 