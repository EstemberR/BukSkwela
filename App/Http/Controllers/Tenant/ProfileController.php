<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Staff;
use App\Models\TenantAdmin;

class ProfileController extends Controller
{
    /**
     * Show the tenant admin profile.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::guard('admin')->user();
        return view('tenant.profile.index', compact('user'));
    }

    /**
     * Update the tenant admin profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenant_admins,email,' . $user->id,
        ]);
        
        try {
            $user->update($validated);
            return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating admin profile', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'There was an error updating your profile.');
        }
    }

    /**
     * Update the tenant admin password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $user->password = Hash::make($request->new_password);
        $user->save();
        
        return redirect()->route('profile.index', ['tenant' => tenant('id')])->with('success', 'Password updated successfully.');
    }

    /**
     * Show the instructor profile.
     *
     * @return \Illuminate\View\View
     */
    public function instructorProfile()
    {
        $user = Auth::guard('staff')->user();
        return view('tenant.Instructors.profile', compact('user'));
    }

    /**
     * Update the instructor profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function instructorProfileUpdate(Request $request)
    {
        $user = Auth::guard('staff')->user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:tenant.staff,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();
        
        return redirect()->route('tenant.instructor.profile', ['tenant' => tenant('id')])->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the instructor password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function instructorPasswordUpdate(Request $request)
    {
        $user = Auth::guard('staff')->user();
        
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $user->password = Hash::make($request->new_password);
        $user->save();
        
        return redirect()->route('tenant.instructor.profile', ['tenant' => tenant('id')])->with('success', 'Password updated successfully.');
    }
} 