<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CreateSuperAdmin::class,
        \App\Console\Commands\UpdateTenantMigrations::class,
        \App\Console\Commands\CreateTenantDatabases::class,
        \App\Console\Commands\TenantDatabaseManagement::class,
        \App\Console\Commands\CheckMySQLConnections::class,
        \App\Console\Commands\SetupTenantDatabase::class,
        \App\Console\Commands\AutoSetupTenantDatabases::class,
        \App\Console\Commands\DirectTenantMigration::class,
        \App\Console\Commands\CheckTenantTables::class,
        \App\Console\Commands\VerifyTenantDatabase::class,
        \App\Console\Commands\VerifyAllTenantDatabases::class,
        \App\Console\Commands\AutoMigrateTenantDatabases::class,
        \App\Console\Commands\TenantMigrateDb::class,
        \App\Console\Commands\TenantListTables::class,
        \App\Console\Commands\TenantVerifyData::class,
        \App\Console\Commands\MoveStaffToTenant::class,
        \App\Console\Commands\VerifyTableStructure::class,
        \App\Console\Commands\VerifyTenantId::class,
        \App\Console\Commands\ReadLogs::class,
        \App\Console\Commands\FixTenantStaffTable::class,
        \App\Console\Commands\FixTenantDatabase::class,
        \App\Console\Commands\SeedTenantDatabase::class,
        \App\Console\Commands\FixStaffTable::class,
        \App\Console\Commands\FixAllTenantDatabases::class,
        \App\Console\Commands\FixCoursesTable::class,
        \App\Console\Commands\FixTenantRelationships::class,
        \App\Console\Commands\CreateTenant::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        
        // Automatically set up databases for any new tenants that don't have them yet
        $schedule->command('tenants:auto-setup --new-only')
            ->daily()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/tenant-auto-setup.log'));
            
        // Automatically migrate all tenant databases to ensure tables are created
        $schedule->command('tenants:auto-migrate')
            ->dailyAt('01:30')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/tenant-auto-migrate.log'));
            
        // Verify all tenant databases are properly configured
        $schedule->command('tenant:verify-all')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/tenant-verify.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
