<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSettings;

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
            
            return view('tenant.Settings.settings', [
                'user' => $user,
                'settings' => $settings
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
     * Save the settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSettings(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::guard('admin')->user() ?? Auth::guard('staff')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to save settings.'
                ], 401);
            }
            
            // Get current tenant
            $currentTenantId = tenant('id');
            
            // Verify tenant context matches
            $requestTenantId = $request->input('tenant_id');
            
            if ($requestTenantId && $requestTenantId !== $currentTenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid tenant context. Settings could not be saved.'
                ], 403);
            }
            
            // Validate the request data
            $validated = $request->validate([
                'dark_mode' => 'boolean',
                'card_style' => 'string|in:square,rounded,glass',
                'font_family' => 'string',
                'font_size' => 'string',
            ]);
            
            // Add tenant ID to the settings data
            $validated['tenant_id'] = $currentTenantId;
            
            // Update or create user settings
            $settings = UserSettings::forTenant($currentTenantId)
                ->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'user_type' => get_class($user)
                    ],
                    $validated
                );
            
            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully',
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Settings save error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving settings.',
                'error' => app()->environment('local', 'development') ? $e->getMessage() : null
            ], 500);
        }
    }
}
