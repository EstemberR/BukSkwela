<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TenantAdmin;
use App\Models\UserSettings;

class InitializeUserSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:init-settings {email?} {tenant_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize user settings for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get arguments or use defaults
        $email = $this->argument('email') ?? 'jorellabeciatnt@gmail.com';
        $tenantId = $this->argument('tenant_id') ?? 'informationtechlogy';

        $this->info("Initializing settings for user {$email} in tenant {$tenantId}");

        // Get the tenant admin
        $user = TenantAdmin::where('tenant_id', $tenantId)
            ->where('email', $email)
            ->first();

        if (!$user) {
            $this->error("User not found with email {$email} in tenant {$tenantId}");
            return 1;
        }

        $this->info("Found user: {$user->email} (ID: {$user->id})");

        // Create or update settings
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

        $this->info("Settings initialized successfully for user {$user->email}");
        return 0;
    }
} 