<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class TenantAdmin extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id'
    ];

    protected $hidden = [
        'password',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function hasRole($role)
    {
        // TenantAdmin always has 'admin' role
        return $role === 'admin';
    }
}