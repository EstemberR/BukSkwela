<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Tenancy;

class TenancyHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('tenancy.helper', function ($app) {
            if ($app->has(Tenancy::class)) {
                return $app->make(Tenancy::class);
            }
            
            // Return a stub implementation that logs errors
            return new class {
                public function __call($method, $args)
                {
                    \Log::error("Tenancy manager not available when calling {$method}");
                    return $this;
                }
                
                public function initialize()
                {
                    \Log::error("Tenancy manager not available when calling initialize");
                    return $this;
                }
                
                public function end()
                {
                    \Log::error("Tenancy manager not available when calling end");
                    return $this;
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 