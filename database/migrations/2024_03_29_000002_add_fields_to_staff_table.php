<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToStaffTable extends Migration
{
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('staff_id')->unique()->after('id');
            $table->enum('role', ['instructor', 'admin', 'staff'])->after('email');
            $table->foreignId('department_id')->nullable()->after('role')
                ->constrained('departments')->onDelete('set null');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('department_id');
        });
    }

    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['staff_id', 'role', 'status']);
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
} 