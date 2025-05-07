<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\TenantAdmin;
use App\Services\UserSettingsCreator;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Events\TenantCreated;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        TenantCreated::class => [
            // We'll handle tenant created in the boot method for more flexibility
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Listen for TenantAdmin creation and automatically create settings
        TenantAdmin::created(function ($admin) {
            try {
                Log::info('TenantAdmin created event triggered', [
                    'admin_id' => $admin->id,
                    'tenant_id' => $admin->tenant_id,
                ]);
                
                $settingsCreator = new UserSettingsCreator();
                $settings = $settingsCreator->createForUser($admin, $admin->tenant_id);
                
                Log::info('Settings automatically created for new TenantAdmin', [
                    'admin_id' => $admin->id,
                    'settings_id' => $settings ? $settings->id : null,
                ]);
            } catch (\Exception $e) {
                Log::error('Error creating settings for new TenantAdmin: ' . $e->getMessage(), [
                    'admin_id' => $admin->id,
                    'exception' => $e,
                ]);
            }
        });
        
        // Listen for tenant creation and create settings for any existing users
        Event::listen(function (TenantCreated $event) {
            try {
                Log::info('Tenant created event received', ['tenant_id' => $event->tenant->id]);
                
                // We'll run this in a queue to avoid slowing down the tenant creation process
                // In a real app, you might want to use a proper queued job here
                dispatch(function () use ($event) {
                    $settingsCreator = new UserSettingsCreator();
                    $results = $settingsCreator->createForTenant($event->tenant->id);
                    
                    Log::info('Settings created for new tenant', [
                        'tenant_id' => $event->tenant->id,
                        'settings_created' => count($results),
                    ]);
                })->afterResponse();
            } catch (\Exception $e) {
                Log::error('Error in TenantCreated event handler: ' . $e->getMessage(), [
                    'tenant_id' => $event->tenant->id,
                    'exception' => $e,
                ]);
            }
        });
    }
}
