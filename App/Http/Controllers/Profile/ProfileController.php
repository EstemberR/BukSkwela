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
            return redirect()->route('tenant.login')
                ->with('error', 'User not found');
        }
        
        // Pass user to the view
        return view('tenant.Profile.Profile', compact('user'));
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

        // Only validate new password and confirmation
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // No need to check current password

        // Hash the new password
        $hashedPassword = Hash::make($request->password);

        // Set the new password and save in TenantAdmin
        $user->password = $hashedPassword;
        $user->save();
        
        // Ensure the tenant_user_credentials table exists
        try {
            // Create the table if it doesn't exist
            DB::connection('tenant')->statement('
                CREATE TABLE IF NOT EXISTS tenant_user_credentials (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                    email VARCHAR(255), 
                    password VARCHAR(255), 
                    tenant_id VARCHAR(255), 
                    tenant_admin_id BIGINT UNSIGNED, 
                    remember_token VARCHAR(100) NULL, 
                    created_at TIMESTAMP NULL, 
                    updated_at TIMESTAMP NULL
                )
            ');
            
            // Check if credential exists
            $credentialExists = DB::connection('tenant')
                ->table('tenant_user_credentials')
                ->where('tenant_admin_id', $user->id)
                ->exists();
            
            if ($credentialExists) {
                // Update existing credential
                DB::connection('tenant')
                    ->table('tenant_user_credentials')
                    ->where('tenant_admin_id', $user->id)
                    ->update([
                        'password' => $hashedPassword,
                        'updated_at' => now()
                    ]);
            } else {
                // Insert new credential
                DB::connection('tenant')
                    ->table('tenant_user_credentials')
                    ->insert([
                        'email' => $user->email,
                        'password' => $hashedPassword,
                        'tenant_id' => tenant('id'),
                        'tenant_admin_id' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
            }
            
            // Log success
            \Illuminate\Support\Facades\Log::info('Password updated successfully for user', [
                'user_id' => $user->id,
                'tenant_id' => tenant('id')
            ]);
            
        } catch (\Exception $e) {
            // Log error but continue
            \Illuminate\Support\Facades\Log::error('Error updating password in tenant_user_credentials: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $user->id,
                'tenant_id' => tenant('id')
            ]);
        }

        return redirect()->route('profile.index', ['tenant' => tenant('id')])
            ->with('success', 'Password updated successfully');
    }
} 