<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequirementCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('requirement_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Probation, Irregular, Regular
            $table->text('description')->nullable();
            $table->string('tenant_id');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('requirement_categories');
    }
} 