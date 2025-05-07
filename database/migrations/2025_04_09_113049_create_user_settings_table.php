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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_type');
            $table->boolean('dark_mode')->default(false);
            $table->string('card_style')->default('square');
            $table->string('font_family')->default('Work Sans, sans-serif');
            $table->string('font_size')->default('14px');
            $table->json('additional_settings')->nullable();
            $table->timestamps();
            
            // Add indexes
            $table->index(['tenant_id', 'user_id', 'user_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
