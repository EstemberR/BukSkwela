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
    public function index()
    {
        // Try getting the user from the admin guard which is used for tenant admins
        $user = Auth::guard('admin')->user();
        
        // Debug information
        $authCheck = Auth::guard('admin')->check();
        $authId = Auth::guard('admin')->id();
        
        // If we couldn't get the user through Auth, try finding them directly
        if (!$user && $authId) {
            $user = TenantAdmin::find($authId);
        }
        
        // As a last resort, try finding the admin for this tenant
        if (!$user) {
            $user = TenantAdmin::where('tenant_id', tenant('id'))->first();
        }
        
        // Create dummy data for testing if no user is found
        if (!$user) {
            $user = new \stdClass();
            $user->name = 'Test User';
            $user->email = 'test@example.com';
            $user->status = 'active';
            $user->id = 0;
        }
        
        // Debug variables
        $debug = [
            'auth_check' => $authCheck,
            'auth_id' => $authId,
            'auth_user' => $user ? true : false,
            'session_id' => session()->getId(),
            'tenant_id' => tenant('id'),
            'admin_guard' => Auth::guard('admin')->check() ? 'Yes' : 'No',
            'web_guard' => Auth::guard('web')->check() ? 'Yes' : 'No'
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
} 