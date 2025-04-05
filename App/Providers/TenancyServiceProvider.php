<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class TenancyServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->configureMiddleware();
        
        // Configure central domains
        config(['tenancy.central_domains' => [
            '127.0.0.1',
            '127.0.0.1:8000',
            'localhost',
            'localhost:8000'
        ]]);

        // Configure tenant identification
        config(['tenancy.identification_driver' => 'domain']);
        
        // Configure session
        config([
            'session.domain' => null,
            'session.same_site' => null,
            'session.secure' => false,
            'session.http_only' => true
        ]);
    }

    protected function configureMiddleware()
    {
        $this->app['router']->aliasMiddleware('tenant', InitializeTenancyByDomain::class);
        $this->app['router']->aliasMiddleware('prevent-access-from-central-domains', PreventAccessFromCentralDomains::class);
    }
}