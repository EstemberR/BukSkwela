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
        Schema::table('courses', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['staff_id']);
            
            // Modify the column to allow null values
            $table->foreignId('staff_id')->nullable()->change();
            
            // Add back the foreign key constraint with nullOnDelete
            $table->foreign('staff_id')
                ->references('id')
                ->on('staff')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Drop the modified foreign key constraint
            $table->dropForeign(['staff_id']);
            
            // Revert the column to not null
            $table->foreignId('staff_id')->change();
            
            // Add back the original foreign key constraint
            $table->foreign('staff_id')
                ->references('id')
                ->on('staff')
                ->onDelete('cascade');
        });
    }
}; 