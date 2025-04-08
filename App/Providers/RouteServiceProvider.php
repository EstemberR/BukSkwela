<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/login';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::bind('student', function ($value) {
            return \App\Models\Student\Student::where('id', $value)
                ->firstOrFail();
        });

        Route::bind('course', function ($value) {
            try {
                $tenant = tenant();
                if (!$tenant) {
                    throw new \Exception('No tenant found');
                }
                
                $tenantId = $tenant->id;
                $dbName = 'tenant_' . $tenantId;
                
                // Get tenant database credentials
                $tenantDB = \App\Models\TenantDatabase::where('tenant_id', $tenantId)->first();
                
                // Configure the tenant database connection
                if ($tenantDB) {
                    \Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $tenantDB->database_name);
                    \Illuminate\Support\Facades\Config::set('database.connections.tenant.username', $tenantDB->database_username);
                    \Illuminate\Support\Facades\Config::set('database.connections.tenant.password', $tenantDB->database_password);
                } else {
                    // Fallback to default credentials
                    \Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $dbName);
                }
                
                // Purge and reconnect to ensure new config is used
                \Illuminate\Support\Facades\DB::purge('tenant');
                \Illuminate\Support\Facades\DB::reconnect('tenant');
                
                // Now query with the configured connection
                return \App\Models\Course\Course::on('tenant')
                    ->where('id', $value)
                    ->firstOrFail();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error in course route binding', [
                    'course_id' => $value,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                abort(404, 'Course not found');
            }
        });

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
        $this->mapTenantRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    protected function mapTenantRoutes()
    {
        Route::middleware(['web'])
             ->namespace($this->namespace)
             ->group(base_path('routes/tenant.php'));
    }
}
