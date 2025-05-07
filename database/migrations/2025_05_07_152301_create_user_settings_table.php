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
            $table->unsignedBigInteger('user_id');
            $table->string('user_type');
            $table->string('tenant_id')->nullable();
            $table->boolean('dark_mode')->default(false);
            $table->string('card_style')->default('default');
            $table->string('font_family')->default('system-ui');
            $table->string('font_size')->default('medium');
            $table->string('dashboard_layout')->default('default');
            $table->json('layout_config')->nullable();
            $table->json('additional_settings')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'user_type']);
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
