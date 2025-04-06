<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateTenantMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:update-migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update tenant migration files with the proper table schemas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating tenant migrations...');

        // Students table migration
        $studentsPath = database_path('migrations/tenant/2025_04_06_112746_tenant_migration_students.php');
        $studentsContent = <<<'EOD'
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique()->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('course_id')->nullable();
            $table->string('department_id')->nullable();
            $table->string('year_level')->nullable();
            $table->string('semester')->nullable();
            $table->string('school_year')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->string('google_drive_folder_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
EOD;
        File::put($studentsPath, $studentsContent);
        $this->info('Updated students migration');

        // Staff table migration
        $staffPath = database_path('migrations/tenant/2025_04_06_112741_tenant_migration_staff.php');
        $staffContent = <<<'EOD'
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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->string('role')->default('staff');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
EOD;
        File::put($staffPath, $staffContent);
        $this->info('Updated staff migration');

        // Courses table migration
        $coursesPath = database_path('migrations/tenant/2025_04_06_112722_tenant_migration_courses.php');
        $coursesContent = <<<'EOD'
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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code')->unique();
            $table->string('course_name');
            $table->text('description')->nullable();
            $table->string('department_id')->nullable();
            $table->integer('units')->default(0);
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
EOD;
        File::put($coursesPath, $coursesContent);
        $this->info('Updated courses migration');

        // Requirements categories table migration
        $categoriesPath = database_path('migrations/tenant/2025_04_06_112735_tenant_migration_requirements_categories.php');
        $categoriesContent = <<<'EOD'
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
        Schema::create('requirement_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_categories');
    }
};
EOD;
        File::put($categoriesPath, $categoriesContent);
        $this->info('Updated requirement categories migration');

        // Requirements table migration
        $requirementsPath = database_path('migrations/tenant/2025_04_06_112729_tenant_migration_requirements.php');
        $requirementsContent = <<<'EOD'
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
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('requirement_categories');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->string('file_type')->nullable();
            $table->integer('max_file_size')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirements');
    }
};
EOD;
        File::put($requirementsPath, $requirementsContent);
        $this->info('Updated requirements migration');

        // Student Requirements table migration
        $studentReqPath = database_path('migrations/tenant/2025_04_06_112751_tenant_migration_student_requirements.php');
        $studentReqContent = <<<'EOD'
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
        Schema::create('student_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('requirement_id')->constrained('requirements');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('google_drive_file_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_requirements');
    }
};
EOD;
        File::put($studentReqPath, $studentReqContent);
        $this->info('Updated student requirements migration');

        // Enable tenant migrations in config
        $this->info('Updating tenancy configuration...');
        
        $this->info('All tenant migrations have been updated!');
        $this->info('To apply these migrations to tenant databases, run:');
        $this->info('php artisan tenants:migrate');
        
        return Command::SUCCESS;
    }
} 