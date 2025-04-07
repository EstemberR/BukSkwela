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
        \App\Console\Commands\MigrateTenants::class,
        \App\Console\Commands\MigrateTenantsBatched::class,
        \App\Console\Commands\MigrateTenantsByDomain::class,
        \App\Console\Commands\CreateTenantDatabases::class,
        \App\Console\Commands\TenantDatabaseManagement::class,
        \App\Console\Commands\CheckMySQLConnections::class,
        \App\Console\Commands\SetupTenantDatabase::class,
        \App\Console\Commands\AutoSetupTenantDatabases::class,
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
