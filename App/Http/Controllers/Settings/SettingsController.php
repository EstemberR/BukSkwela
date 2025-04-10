<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\UserSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Add authentication middleware - this checks for logged in users
        $this->middleware(['auth:admin,staff']);
        
        // Check if the layout_config column exists, if not run the migration
        try {
            if (!\Schema::hasColumn('user_settings', 'layout_config')) {
                $this->runLayoutMigration();
            }
        } catch (\Exception $e) {
            \Log::error('Error checking or running layout migration: ' . $e->getMessage());
        }
    }

    /**
     * Run the migration to add layout-related columns.
     */
    private function runLayoutMigration()
    {
        try {
            \Schema::table('user_settings', function (\Illuminate\Database\Schema\Blueprint $table) {
                if (!\Schema::hasColumn('user_settings', 'dashboard_layout')) {
                    $table->string('dashboard_layout')->nullable()->after('font_size');
                }
                
                if (!\Schema::hasColumn('user_settings', 'layout_config')) {
                    $table->text('layout_config')->nullable()->after('dashboard_layout');
                }
            });
            
            \Log::info('Layout migration columns added successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to run layout migration: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Display the settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Get the authenticated user
            $user = Auth::guard('admin')->user() ?? Auth::guard('staff')->user();
            
            if (!$user) {
                return redirect()->route('tenant.login')->with('error', 'You must be logged in to access settings.');
            }
            
            // Get current tenant
            $currentTenantId = tenant('id');
            
            // Get user settings or create default settings
            $settings = UserSettings::forTenant($currentTenantId)
                ->firstOrNew([
                    'user_id' => $user->id,
                    'user_type' => get_class($user)
                ]);
            
            // Ensure the tenant_id is set for new settings
            if (!$settings->exists) {
                $settings->tenant_id = $currentTenantId;
                // Set default values for new settings
                $settings->dark_mode = false;
                $settings->card_style = 'square';
                $settings->font_family = 'Work Sans, sans-serif';
                $settings->font_size = '14px';
            }
            
            // Get available dashboard components
            $availableComponents = $this->getAvailableDashboardComponents();
            
            return view('tenant.Settings.settings', [
                'user' => $user,
                'settings' => $settings,
                'availableComponents' => $availableComponents
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Settings page error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a view with error details
            return view('tenant.error', [
                'message' => 'There was a problem loading your settings.',
                'details' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
        }
    }
    
    /**
     * Get available components from dashboard view.
     *
     * @return array
     */
    private function getAvailableDashboardComponents()
    {
        $components = [
            'statistics' => [],
            'content' => [],
            'bottom' => []
        ];
        
        try {
            // Path to dashboard blade file
            $dashboardPath = resource_path('views/tenant/dashboard.blade.php');
            
            if (!file_exists($dashboardPath)) {
                \Log::warning('Dashboard file not found: ' . $dashboardPath);
                return $this->getDefaultComponents();
            }
            
            $dashboardContent = file_get_contents($dashboardPath);
            
            // Extract statistics components (card statistics in the top row)
            if (preg_match_all('/<div class="col-md-3">\s*<div class="card.*?<i class="fas fa-(.*?)\s.*?<h6.*?>(.*?)<\/h6>/s', $dashboardContent, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $icon = $match[1];
                    $title = trim(strip_tags($match[2]));
                    $type = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $title));
                    
                    $components['statistics'][] = [
                        'type' => $type,
                        'title' => $title,
                        'icon' => $icon,
                        'enabled' => true,
                        'order' => count($components['statistics'])
                    ];
                }
            }
            
            // Extract content components (enrolled cards)
            if (preg_match_all('/<div class="enrolled-card.*?>\s*<p class="title.*?>(.*?)<\/p>/s', $dashboardContent, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $title = trim(strip_tags($match[1]));
                    $type = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $title));
                    
                    // Find associated icon if available
                    $icon = 'list';
                    if (stripos($title, 'student') !== false) {
                        $icon = 'user-graduate';
                    } elseif (stripos($title, 'course') !== false) {
                        $icon = 'book';
                    } elseif (stripos($title, 'requirement') !== false) {
                        $icon = 'clipboard-list';
                    } elseif (stripos($title, 'calendar') !== false || stripos($type, 'calendar') !== false) {
                        $icon = 'calendar';
                        $type = 'calendar';
                    } elseif (stripos($title, 'activity') !== false) {
                        $icon = 'clock';
                        $type = 'recent-activity';
                    }
                    
                    $components['content'][] = [
                        'type' => $type,
                        'title' => $title,
                        'icon' => $icon,
                        'enabled' => true,
                        'order' => count($components['content'])
                    ];
                }
            }
            
            // If no components were found, return defaults
            if (empty($components['statistics']) && empty($components['content'])) {
                return $this->getDefaultComponents();
            }
            
            return $components;
            
        } catch (\Exception $e) {
            \Log::error('Error extracting dashboard components: ' . $e->getMessage());
            return $this->getDefaultComponents();
        }
    }
    
    /**
     * Get default dashboard components based on actual dashboard layout.
     *
     * @return array
     */
    private function getDefaultComponents()
    {
        return [
            'statistics' => [
                ['type' => 'total-instructors', 'title' => 'Total Instructors', 'icon' => 'chalkboard-teacher'],
                ['type' => 'total-students', 'title' => 'Total Students', 'icon' => 'user-graduate'],
                ['type' => 'pending-requirements', 'title' => 'Pending Requirements', 'icon' => 'clipboard-list'],
                ['type' => 'active-courses', 'title' => 'Active Courses', 'icon' => 'book']
            ],
            'content' => [
                ['type' => 'recent-enrolled-students', 'title' => 'Recent Enrolled Students', 'icon' => 'user-graduate'],
                ['type' => 'available-courses', 'title' => 'Available Courses', 'icon' => 'book'],
                ['type' => 'requirements-submitted', 'title' => 'Requirements Submitted', 'icon' => 'clipboard-list']
            ],
            'bottom' => []
        ];
    }
    
    /**
     * Save user settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSettings(Request $request)
    {
        try {
            \Log::info('Settings save request received', $request->all());
            
            // Get authenticated user
            $user = Auth::guard('admin')->user() ?? Auth::guard('staff')->user();
            
            // If no user is authenticated, return error
            if (!$user) {
                \Log::warning('No authenticated user found');
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }
            
            \Log::info('Authenticated user', ['id' => $user->id, 'type' => get_class($user)]);
            
            // Ensure tenant context
            $tenantId = $request->input('tenant_id') ?? tenant('id');
            
            if (!$tenantId) {
                \Log::error('Tenant ID not provided and could not be determined');
                return response()->json(['success' => false, 'message' => 'Tenant ID is required'], 400);
            }
            
            \Log::info('Current tenant', ['id' => $tenantId]);
            
            // Extract settings from request
            $darkMode = $request->has('dark_mode') ? ($request->input('dark_mode') === 'on' || $request->input('dark_mode') === true) : null;
            $layoutConfig = $request->input('layout_config');
            $dashboardLayout = $request->input('dashboard_layout');
            $themeColor = $request->input('theme_color');
            $fontSize = $request->input('font_size');
            $fontFamily = $request->input('font_family');
            $cardStyle = $request->input('card_style'); // Make sure we get card_style
            $notificationSound = $request->has('notification_sound') ? ($request->input('notification_sound') === 'on' || $request->input('notification_sound') === true) : null;
            $emailNotifications = $request->has('email_notifications') ? ($request->input('email_notifications') === 'on' || $request->input('email_notifications') === true) : null;
            
            // Check if the user already has saved settings with this tenant
            $settings = UserSettings::forTenant($tenantId)
                ->where('user_id', $user->id)
                ->where('user_type', get_class($user))
                ->first();
            
            if ($settings) {
                \Log::info('Updating existing settings for user: ' . $user->id);
                
                // Update existing settings
                $updateData = [];
                
                if ($darkMode !== null) {
                    $updateData['dark_mode'] = $darkMode;
                }
                
                if ($layoutConfig !== null) {
                    $updateData['layout_config'] = $layoutConfig;
                }
                
                if ($dashboardLayout !== null) {
                    $updateData['dashboard_layout'] = $dashboardLayout;
                }
                
                if ($themeColor !== null) {
                    $updateData['theme_color'] = $themeColor;
                }
                
                if ($fontSize !== null) {
                    $updateData['font_size'] = $fontSize;
                }
                
                if ($fontFamily !== null) {
                    $updateData['font_family'] = $fontFamily;
                }
                
                if ($cardStyle !== null) { // Add card_style to update data
                    $updateData['card_style'] = $cardStyle;
                }
                
                if ($notificationSound !== null) {
                    $updateData['notification_sound'] = $notificationSound;
                }
                
                if ($emailNotifications !== null) {
                    $updateData['email_notifications'] = $emailNotifications;
                }
                
                // Debug log
                \Log::info('Update data: ' . json_encode($updateData));
                
                try {
                    // Always ensure tenant ID is set correctly on update
                    $settings->tenant_id = $tenantId;
                    $settings->update($updateData);
                    
                    // If dark mode preference changed, trigger browser event
                    if (isset($updateData['dark_mode'])) {
                        $this->storeDarkModePreference($darkMode);
                    }
                    
                    \Log::info('Settings updated successfully');
                    return response()->json(['success' => true, 'message' => 'Settings updated successfully']);
                } catch (\Exception $e) {
                    \Log::error('Error updating settings: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Error updating settings: ' . $e->getMessage()], 500);
                }
            } else {
                \Log::info('Creating new settings for user: ' . $user->id);

                // Construct INSERT operation with raw SQL to ensure tenant context
                try {
                    $sql = "INSERT INTO user_settings (user_id, user_type, tenant_id, created_at, updated_at";
                    $values = "VALUES (?, ?, ?, NOW(), NOW()";
                    $bindings = [$user->id, get_class($user), $tenantId];

                    if ($layoutConfig !== null) {
                        $sql .= ", layout_config";
                        $values .= ", ?";
                        $bindings[] = $layoutConfig;
                    }

                    if ($darkMode !== null) {
                        $sql .= ", dark_mode";
                        $values .= ", ?";
                        $bindings[] = $darkMode;
                    }

                    if ($themeColor !== null) {
                        $sql .= ", theme_color";
                        $values .= ", ?";
                        $bindings[] = $themeColor;
                    }
                    
                    if ($fontFamily !== null) {
                        $sql .= ", font_family";
                        $values .= ", ?";
                        $bindings[] = $fontFamily;
                    }

                    if ($fontSize !== null) {
                        $sql .= ", font_size";
                        $values .= ", ?";
                        $bindings[] = $fontSize;
                    }
                    
                    if ($cardStyle !== null) { // Add card_style to SQL
                        $sql .= ", card_style";
                        $values .= ", ?";
                        $bindings[] = $cardStyle;
                    }
                    
                    if ($dashboardLayout !== null) {
                        $sql .= ", dashboard_layout";
                        $values .= ", ?";
                        $bindings[] = $dashboardLayout;
                    }

                    if ($notificationSound !== null) {
                        $sql .= ", notification_sound";
                        $values .= ", ?";
                        $bindings[] = $notificationSound;
                    }

                    if ($emailNotifications !== null) {
                        $sql .= ", email_notifications";
                        $values .= ", ?";
                        $bindings[] = $emailNotifications;
                    }

                    $sql .= ") " . $values . ")";

                    \Log::info('Executing SQL: ' . $sql);
                    \Log::info('With bindings: ' . json_encode($bindings));

                    $result = DB::insert($sql, $bindings);

                    if ($result) {
                        // If dark mode preference set, trigger browser event
                        if ($darkMode !== null) {
                            $this->storeDarkModePreference($darkMode);
                        }
                        
                        \Log::info('Settings created successfully');
                        return response()->json(['success' => true, 'message' => 'Settings created successfully']);
                    } else {
                        \Log::error('Failed to create settings - no rows affected');
                        return response()->json(['success' => false, 'message' => 'Failed to create settings'], 500);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error creating settings: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Error creating settings: ' . $e->getMessage()], 500);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Unexpected error in saveSettings: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unexpected error: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Store dark mode preference in browser local storage
     *
     * @param bool $isDarkMode Dark mode enabled status
     * @return void
     */
    private function storeDarkModePreference($isDarkMode)
    {
        // Store the preference in session for flash messaging
        session()->flash('dark_mode_preference', $isDarkMode ? 'enabled' : 'disabled');
        
        // Also store in a regular session variable for API responses
        session(['dark_mode_enabled' => $isDarkMode]);
        
        \Log::info('Dark mode preference set: ' . ($isDarkMode ? 'enabled' : 'disabled'));
    }
    
    /**
     * Save the dashboard layout configuration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveLayout(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::guard('admin')->user() ?? Auth::guard('staff')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to save layout settings.'
                ], 401);
            }
            
            // Get current tenant
            $currentTenantId = tenant('id');
            
            // Log received data for debugging
            \Log::info('Layout save request received', [
                'dashboard_layout' => $request->input('dashboard_layout'),
                'has_layout_config' => $request->has('layout_config'),
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'tenant_id' => $currentTenantId
            ]);
            
            // Validate the request data
            $validated = $request->validate([
                'layout_config' => 'required|string',
                'dashboard_layout' => 'nullable|string|in:standard,compact,modern',
            ]);
            
            // Ensure layout_config is valid JSON
            try {
                $layoutConfig = json_decode($validated['layout_config'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON in layout_config: ' . json_last_error_msg());
                }
            } catch (\Exception $e) {
                \Log::error('Invalid layout_config JSON: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid layout configuration format.',
                    'error' => app()->environment('local', 'development') ? $e->getMessage() : null
                ], 400);
            }
            
            // Get the user settings
            $settings = UserSettings::forTenant($currentTenantId)
                ->where('user_id', $user->id)
                ->where('user_type', get_class($user))
                ->first();
            
            // If settings don't exist, create new with all required fields
            if (!$settings) {
                $settings = new UserSettings();
                $settings->user_id = $user->id;
                $settings->user_type = get_class($user);
                $settings->tenant_id = $currentTenantId; // Set this explicitly
                
                // Set some defaults
                $settings->dark_mode = false;
                $settings->card_style = 'square';
                $settings->font_family = 'Work Sans, sans-serif';
                $settings->font_size = '14px';
                
                // Log that we're creating new settings
                \Log::info('Creating new UserSettings record', [
                    'user_id' => $user->id,
                    'tenant_id' => $currentTenantId
                ]);
            }
            
            // Always ensure tenant_id is set correctly
            $settings->tenant_id = $currentTenantId;
            
            // Update layout configuration
            $settings->layout_config = $validated['layout_config'];
            
            // Update dashboard layout type if provided
            if (isset($validated['dashboard_layout'])) {
                // Explicit debug log
                \Log::info('Setting dashboard_layout to: ' . $validated['dashboard_layout']);
                $settings->dashboard_layout = $validated['dashboard_layout'];
            } else {
                \Log::warning('dashboard_layout not set in validated data');
            }
            
            // Save and check for errors - try direct DB approach
            try {
                // If this is a new record, use direct DB insert
                if (!isset($settings->id) || !$settings->id) {
                    \Log::info('Using direct DB insert for new settings record', [
                        'tenant_id' => $currentTenantId,
                        'user_id' => $user->id,
                        'user_type' => get_class($user)
                    ]);
                    
                    $now = now();
                    $nowFormatted = $now->format('Y-m-d H:i:s');
                    
                    // Direct raw SQL to bypass any binding issues
                    $escapedTenantId = DB::connection()->getPdo()->quote($currentTenantId);
                    $escapedUserId = DB::connection()->getPdo()->quote($user->id);
                    $escapedUserType = DB::connection()->getPdo()->quote(get_class($user));
                    $escapedDarkMode = $settings->dark_mode ? '1' : '0';
                    $escapedCardStyle = DB::connection()->getPdo()->quote($settings->card_style);
                    $escapedFontFamily = DB::connection()->getPdo()->quote($settings->font_family);
                    $escapedFontSize = DB::connection()->getPdo()->quote($settings->font_size);
                    $escapedLayoutConfig = DB::connection()->getPdo()->quote($settings->layout_config);
                    $escapedDashboardLayout = DB::connection()->getPdo()->quote($settings->dashboard_layout);
                    $escapedNow = DB::connection()->getPdo()->quote($nowFormatted);
                    
                    // Construct SQL with literal values
                    $sql = "INSERT INTO user_settings 
                            (user_id, user_type, tenant_id, dark_mode, card_style, font_family, font_size, layout_config, dashboard_layout, created_at, updated_at) 
                            VALUES (
                                {$escapedUserId}, 
                                {$escapedUserType}, 
                                {$escapedTenantId}, 
                                {$escapedDarkMode}, 
                                {$escapedCardStyle}, 
                                {$escapedFontFamily}, 
                                {$escapedFontSize}, 
                                {$escapedLayoutConfig}, 
                                {$escapedDashboardLayout}, 
                                {$escapedNow}, 
                                {$escapedNow}
                            )";
                    
                    // Log the raw SQL
                    \Log::info('Executing raw SQL insert: ' . $sql);
                    
                    // Execute the raw SQL
                    $insertResult = DB::statement($sql);
                    
                    \Log::info('DB raw statement result', ['result' => $insertResult]);
                    
                    // Get the last inserted ID
                    $lastId = DB::getPdo()->lastInsertId();
                    
                    if ($lastId) {
                        $settings->id = $lastId;
                        \Log::info('Got new record ID from lastInsertId', ['id' => $settings->id]);
                        
                        // Fetch the complete record from the database after insert
                        // to ensure we have all fields including updated_at
                        try {
                            $freshSettings = UserSettings::find($lastId);
                            if ($freshSettings) {
                                $settings = $freshSettings;
                                \Log::info('Successfully fetched fresh settings after insert', ['id' => $settings->id]);
                            } else {
                                \Log::warning('Could not find settings with ID after insert', ['id' => $lastId]);
                                // In case we couldn't get a fresh settings, manually set updated_at
                                $settings->updated_at = $now;
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Error fetching fresh settings: ' . $e->getMessage());
                            // In case of an error, manually set updated_at
                            $settings->updated_at = $now;
                        }
                    } else {
                        \Log::warning('Failed to get lastInsertId');
                        // Ensure we have an updated_at value even if insert failed
                        $settings->updated_at = $now;
                    }
                } else {
                    // For existing records, use update
                    \Log::info('Updating existing record', ['id' => $settings->id]);
                    
                    $now = now();
                    
                    $updateResult = DB::table('user_settings')
                        ->where('id', $settings->id)
                        ->update([
                            'tenant_id' => $currentTenantId,
                            'layout_config' => $settings->layout_config,
                            'dashboard_layout' => $settings->dashboard_layout,
                            'updated_at' => $now
                        ]);
                    
                    \Log::info('DB update result', ['result' => $updateResult]);
                    
                    // Refresh the Eloquent model to get the updated data
                    if ($updateResult) {
                        try {
                            $freshSettings = UserSettings::find($settings->id);
                            if ($freshSettings) {
                                $settings = $freshSettings;
                                \Log::info('Successfully fetched fresh settings after update', ['id' => $settings->id]);
                            } else {
                                \Log::warning('Could not find settings with ID after update', ['id' => $settings->id]);
                                // Manually set updated_at if we couldn't fetch the updated record
                                $settings->updated_at = $now;
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Error fetching fresh settings after update: ' . $e->getMessage());
                            // Manually set updated_at in case of error
                            $settings->updated_at = $now;
                        }
                    } else {
                        \Log::warning('Update operation did not affect any rows');
                        // Ensure we have a valid updated_at value even if update didn't affect rows
                        $settings->updated_at = $now;
                    }
                }
            } catch (\Exception $saveException) {
                \Log::error('Exception while saving settings: ' . $saveException->getMessage(), [
                    'settings' => $settings->toArray(),
                    'tenant_id' => $currentTenantId,
                    'user_id' => $user->id
                ]);
                throw $saveException;
            }
            
            // No need to reload for Eloquent operations since we've already done that in the try block
            // for both insert and update operations
            
            \Log::info('Layout settings saved successfully', [
                'settings_id' => $settings->id,
                'dashboard_layout' => $settings->dashboard_layout,
                'model_class' => get_class($settings)
            ]);
            
            // Create response with appropriate handling for potentially null updated_at
            $updatedAt = $settings->updated_at ? $settings->updated_at->toIso8601String() : now()->toIso8601String();
            
            return response()->json([
                'success' => true,
                'message' => 'Dashboard layout saved successfully',
                'dashboard_layout' => $settings->dashboard_layout,
                'updated_at' => $updatedAt
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Layout save error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the layout.',
                'error' => app()->environment('local', 'development') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get the dashboard layout configuration.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLayout()
    {
        try {
            // Get the authenticated user (try multiple guards)
            $user = Auth::guard('admin')->user() ?? Auth::guard('staff')->user() ?? Auth::guard('web')->user();
            
            if (!$user) {
                \Log::warning('Layout retrieval failed - no authenticated user');
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401);
            }
            
            // Get current tenant
            $currentTenantId = tenant('id');
            
            // Ensure we have a valid tenant ID
            if (empty($currentTenantId)) {
                \Log::warning('Invalid tenant ID detected for user', [
                    'user_id' => $user->id,
                    'user_type' => get_class($user)
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid tenant context',
                    'default_provided' => true,
                    'dashboard_layout' => 'standard'
                ]);
            }
            
            // Log the request
            \Log::info('Layout retrieval request', [
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'tenant_id' => $currentTenantId
            ]);
            
            // Get user settings - try both with and without tenant_id to handle migration cases
            $settings = UserSettings::where('user_id', $user->id)
                ->where('user_type', get_class($user))
                ->where(function($query) use ($currentTenantId) {
                    $query->where('tenant_id', $currentTenantId)
                          ->orWhereNull('tenant_id');
                })
                ->first();
            
            // Double check if the tenant_id is correct, and update it if needed
            if ($settings && empty($settings->tenant_id)) {
                try {
                    $settings->tenant_id = $currentTenantId;
                    $settings->save();
                    \Log::info('Updated missing tenant_id in user settings', [
                        'settings_id' => $settings->id,
                        'tenant_id' => $currentTenantId
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to update tenant_id in user settings: ' . $e->getMessage());
                }
            }
            
            if (!$settings) {
                \Log::info('No user settings found, returning default layout', [
                    'user_id' => $user->id,
                    'user_type' => get_class($user),
                    'tenant_id' => $currentTenantId
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'No settings found, using default',
                    'default_provided' => true,
                    'dashboard_layout' => 'standard',
                    'layout_config' => null
                ]);
            }
            
            // Ensure dashboard_layout has a valid value
            $dashboardLayout = $settings->dashboard_layout;
            if (!in_array($dashboardLayout, ['standard', 'compact', 'modern'])) {
                $dashboardLayout = 'standard';
                \Log::info('Invalid dashboard_layout detected, using standard', [
                    'settings_id' => $settings->id,
                    'invalid_value' => $settings->dashboard_layout
                ]);
            }
            
            if (!$settings->layout_config) {
                \Log::info('No layout configuration found in settings', [
                    'settings_id' => $settings->id,
                    'dashboard_layout' => $dashboardLayout
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Layout type found, but no custom configuration',
                    'dashboard_layout' => $dashboardLayout,
                    'layout_config' => null
                ]);
            }
            
            \Log::info('Layout retrieved successfully', [
                'settings_id' => $settings->id,
                'dashboard_layout' => $dashboardLayout,
                'has_layout_config' => !empty($settings->layout_config)
            ]);
        
        return response()->json([
            'success' => true,
                'layout_config' => $settings->layout_config,
                'dashboard_layout' => $dashboardLayout,
                'last_updated' => $settings->updated_at->toIso8601String()
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Layout retrieval error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the layout.',
                'error' => app()->environment('local', 'development') ? $e->getMessage() : null,
                'dashboard_layout' => 'standard'
            ], 500);
        }
    }
}
