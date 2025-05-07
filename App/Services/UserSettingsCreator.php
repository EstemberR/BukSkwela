<?php

namespace App\Services;

use App\Models\UserSettings;
use App\Models\TenantAdmin;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class UserSettingsCreator
{
    /**
     * Create default settings for a user
     *
     * @param mixed $user The user model
     * @param string|null $tenantId The tenant ID
     * @return UserSettings
     */
    public function createForUser($user, $tenantId = null)
    {
        if (!$user) {
            Log::warning('Attempted to create settings for null user');
            return null;
        }
        
        // Determine tenant ID if not provided
        if (!$tenantId && method_exists($user, 'getAttribute') && $user->getAttribute('tenant_id')) {
            $tenantId = $user->getAttribute('tenant_id');
        }
        
        // Try to get tenant ID from global function if available
        if (!$tenantId && function_exists('tenant')) {
            try {
                $tenant = tenant();
                if ($tenant && isset($tenant->id)) {
                    $tenantId = $tenant->id;
                }
            } catch (\Exception $e) {
                Log::error('Error getting tenant ID: ' . $e->getMessage());
            }
        }

        Log::info('Creating settings for user', [
            'user_id' => $user->id,
            'user_type' => get_class($user),
            'tenant_id' => $tenantId
        ]);

        // Create or find settings
        $settings = UserSettings::firstOrNew([
            'user_id' => $user->id,
            'user_type' => get_class($user)
        ]);

        // Set defaults
        $settings->tenant_id = $tenantId;
        $settings->dark_mode = false;
        $settings->card_style = 'square';
        $settings->font_family = 'Work Sans, sans-serif';
        $settings->font_size = '14px';
        $settings->dashboard_layout = 'standard';
        $settings->layout_config = json_encode([
            'statistics' => [
                ['type' => 'total-instructors', 'title' => 'Total Instructors', 'icon' => 'chalkboard-teacher', 'enabled' => true, 'order' => 0],
                ['type' => 'total-students', 'title' => 'Total Students', 'icon' => 'user-graduate', 'enabled' => true, 'order' => 1],
                ['type' => 'pending-requirements', 'title' => 'Pending Requirements', 'icon' => 'clipboard-list', 'enabled' => true, 'order' => 2],
                ['type' => 'active-courses', 'title' => 'Active Courses', 'icon' => 'book', 'enabled' => true, 'order' => 3]
            ],
            'content' => [
                ['type' => 'recent-enrolled-students', 'title' => 'Recent Enrolled Students', 'icon' => 'user-graduate', 'enabled' => true, 'order' => 0],
                ['type' => 'available-courses', 'title' => 'Available Courses', 'icon' => 'book', 'enabled' => true, 'order' => 1],
                ['type' => 'requirements-submitted', 'title' => 'Requirements Submitted', 'icon' => 'clipboard-list', 'enabled' => true, 'order' => 2]
            ]
        ]);

        // Save the settings
        $settings->save();

        Log::info('Settings created successfully', [
            'settings_id' => $settings->id,
            'user_id' => $user->id
        ]);

        return $settings;
    }

    /**
     * Create settings for all existing users in a tenant
     *
     * @param string $tenantId
     * @return array
     */
    public function createForTenant($tenantId)
    {
        $results = [];
        
        // Get all admins for this tenant
        $admins = TenantAdmin::where('tenant_id', $tenantId)->get();
        
        foreach ($admins as $admin) {
            $settings = $this->createForUser($admin, $tenantId);
            if ($settings) {
                $results[] = [
                    'user_id' => $admin->id,
                    'settings_id' => $settings->id,
                    'status' => 'created'
                ];
            }
        }
        
        return $results;
    }
    
    /**
     * Create settings for all tenants
     *
     * @return array
     */
    public function createForAllTenants()
    {
        $results = [];
        
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            $result = $this->createForTenant($tenant->id);
            $results[$tenant->id] = $result;
        }
        
        return $results;
    }
} 