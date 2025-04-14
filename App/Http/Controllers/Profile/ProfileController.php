<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TenantAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ProfileController extends Controller
{
    /**
     * Display the profile page
     */
    public function index()
    {
        // First, let's handle various ways to get the user
        $authCheck = Auth::guard('admin')->check();
        $authId = $authCheck ? Auth::guard('admin')->id() : null;
        $user = $authCheck ? Auth::guard('admin')->user() : null;
        
        // If we couldn't get the user, try finding the admin for this tenant
        if (!$user) {
            $user = TenantAdmin::where('tenant_id', tenant('id'))->first();
        }
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User not found');
        }
        
        // Check if tenant is premium from the tenant model directly
        $tenant = tenant();
        
        // Force refresh tenant data to ensure we have the latest
        if ($tenant && $tenant instanceof \App\Models\Tenant) {
            $tenant->refresh();
        }
        
        // Set premium status in session if the tenant has a premium subscription
        if ($tenant && $tenant->subscription_plan === 'premium') {
            session(['is_premium' => true]);
        }
        
        // Debug variables
        $debug = [
            'auth_check' => $authCheck,
            'auth_id' => $authId,
            'auth_user' => $user ? true : false,
            'session_id' => session()->getId(),
            'tenant_id' => tenant('id'),
            'admin_guard' => Auth::guard('admin')->check() ? 'Yes' : 'No',
            'web_guard' => Auth::guard('web')->check() ? 'Yes' : 'No',
            'is_premium_session' => session('is_premium') ? 'Yes' : 'No',
            'tenant_subscription' => $tenant ? ($tenant->subscription_plan ?? 'None') : 'None'
        ];
        
        // Pass everything to the view
        return view('tenant.Profile.Profile', compact('user', 'debug'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        // If we couldn't get the user, try finding the admin for this tenant
        if (!$user) {
            $user = TenantAdmin::where('tenant_id', tenant('id'))->first();
        }
        
        if (!$user) {
            return redirect()->route('profile.index', ['tenant' => tenant('id')])
                ->with('error', 'User not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tenant_admins,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('profile.index', ['tenant' => tenant('id')])
            ->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        // If we couldn't get the user, try finding the admin for this tenant
        if (!$user) {
            $user = TenantAdmin::where('tenant_id', tenant('id'))->first();
        }
        
        if (!$user) {
            return redirect()->route('profile.index', ['tenant' => tenant('id')])
                ->with('error', 'User not found');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile.index', ['tenant' => tenant('id')])
            ->with('success', 'Password updated successfully');
    }

    /**
     * Update the tenant's subscription plan
     */
    public function updateSubscription(Request $request)
    {
        try {
            // Ensure subscription_requests table exists
            $this->ensureSubscriptionTablesExist();
            
            $tenant = tenant();
            $tenantId = $tenant ? $tenant->id : null;
            
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }
            
            // Validate request data
            $request->validate([
                'subscription_plan' => 'required|string|in:basic,premium,enterprise',
                'user_name' => 'nullable|string',
                'user_email' => 'nullable|email',
                'message' => 'nullable|string',
                'set_session' => 'nullable|boolean'
            ]);
            
            // Create a subscription request record - with try/catch for safety
            try {
                DB::table('subscription_requests')->insert([
                    'tenant_id' => $tenantId,
                    'user_id' => $request->user_id ?? Auth::guard('admin')->id(),
                    'current_plan' => $tenant->subscription_plan ?? 'basic',
                    'requested_plan' => $request->subscription_plan,
                    'user_name' => $request->user_name,
                    'user_email' => $request->user_email,
                    'message' => $request->message,
                    'status' => 'approved', // Set to approved directly
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Log the error but continue
                \Log::error('Error creating subscription request', [
                    'error' => $e->getMessage(), 
                    'tenant_id' => $tenantId
                ]);
            }
            
            // Set session variable for premium status if requested
            if ($request->has('set_session') && $request->set_session) {
                session(['is_premium' => true]);
            }
            
            // Update the subscription plan in the tenants table
            // This ensures the change persists across page refreshes
            DB::table('tenants')
                ->where('id', $tenantId)
                ->update([
                    'subscription_plan' => $request->subscription_plan,
                    'updated_at' => now()
                ]);
            
            // Also update the data JSON field to ensure consistency
            $data = $tenant->data ?? [];
            $data['subscription_plan'] = $request->subscription_plan;
            
            // Add subscription end date (1 year from now for premium)
            $data['subscription_ends_at'] = now()->addYear()->format('Y-m-d H:i:s');
            $data['payment_status'] = 'paid';
            
            DB::table('tenants')
                ->where('id', $tenantId)
                ->update([
                    'data' => json_encode($data),
                    'updated_at' => now()
                ]);
            
            // Record this activity for admin tracking - with try/catch for safety
            try {
                DB::table('tenant_activities')->insert([
                    'tenant_id' => $tenantId,
                    'user_id' => $request->user_id ?? Auth::guard('admin')->id(),
                    'activity' => 'subscription_change',
                    'description' => 'Subscription plan updated from ' . 
                                    ($tenant->subscription_plan ?? 'basic') . 
                                    ' to ' . $request->subscription_plan,
                    'created_at' => now()
                ]);
            } catch (\Exception $e) {
                // Log the error but continue
                \Log::error('Error creating tenant activity', [
                    'error' => $e->getMessage(), 
                    'tenant_id' => $tenantId
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Subscription updated successfully',
                'data' => [
                    'tenant_id' => $tenantId,
                    'subscription_plan' => $request->subscription_plan,
                    'session_status' => session('is_premium')
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating subscription plan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Ensure that the required tables exist
     */
    private function ensureSubscriptionTablesExist()
    {
        // Check if subscription_requests table exists
        if (!Schema::hasTable('subscription_requests')) {
            Schema::create('subscription_requests', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('current_plan')->default('basic');
                $table->string('requested_plan');
                $table->string('user_name')->nullable();
                $table->string('user_email')->nullable();
                $table->text('message')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamps();
            });
        }
        
        // Check if tenant_activities table exists
        if (!Schema::hasTable('tenant_activities')) {
            Schema::create('tenant_activities', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('activity');
                $table->text('description')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
            });
        }
    }
} 