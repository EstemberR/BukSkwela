<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Stancl\Tenancy\Events\TenantCreated;

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
        \App\Console\Commands\MigrateTenantToSeparateDb::class,
        \App\Console\Commands\CheckTenantDatabase::class,
        \App\Console\Commands\FixUserPassword::class,
        \App\Console\Commands\InitializeUserSettings::class,
        \App\Console\Commands\FixAllUserSettings::class,
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
            
        // Make sure all users have settings
        $schedule->command('user:fix-all-settings')
            ->dailyAt('03:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/user-settings.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    
    /**
     * Register the Tenancy listeners
     */
    private function registerTenancyListeners()
    {
        // Use the application's event dispatcher instead of the facade
        $this->app->make('events')->listen(TenantCreated::class, function (TenantCreated $event) {
            // Use the application's artisan instance instead of the facade
            $this->app->make('Illuminate\Contracts\Console\Kernel')->call('tenants:create-student-info-table', [
                'tenant_id' => $event->tenant->id
            ]);
        });
        
        // Additional listeners can be registered here
    }

    /**
     * Constructor to register tenancy listeners
     */
    public function __construct(\Illuminate\Contracts\Foundation\Application $app, \Illuminate\Contracts\Events\Dispatcher $events)
    {
        parent::__construct($app, $events);
        
        // Moving this back as we're now using dependency injection instead of facades
        $this->registerTenancyListeners();
    }
    
    /**
     * Bootstrap the application for artisan commands.
     *
     * @return void
     */
    public function bootstrap()
    {
        parent::bootstrap();
    }
}
