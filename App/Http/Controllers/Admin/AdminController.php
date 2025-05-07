<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Make sure the tenant information is available for all admin views
     *
     * @param array $data
     * @return array
     */
    protected function withTenantData(array $data = [])
    {
        return array_merge($data, [
            'tenant_id' => session('current_tenant_id'),
            'tenant_name' => session('current_tenant_name'),
            'admin' => Auth::guard('admin')->user(),
        ]);
    }
} 