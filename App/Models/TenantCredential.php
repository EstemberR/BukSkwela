<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TenantCredential extends Authenticatable
{
    protected $fillable = [
        'email',
        'password',
        'tenant_id',
        'tenant_admin_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the tenant admin associated with this credential.
     */
    public function tenantAdmin()
    {
        return $this->belongsTo(TenantAdmin::class, 'tenant_admin_id');
    }

    /**
     * Get the tenant associated with this credential.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
