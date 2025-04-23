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