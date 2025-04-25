<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $table = 'tenants';

    protected $fillable = [
        'id',
        'data',
        'tenant_name',
        'tenant_email',
        'status',
        'subscription_plan'
    ];

    protected $casts = [
        'data' => 'json'
    ];

    public function getDataAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }

    /**
     * Get the name from the data JSON if tenant_name is null
     */
    public function getTenantNameAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        $data = $this->data;
        return $data['name'] ?? null;
    }
    
    /**
     * Get the email from the data JSON if tenant_email is null
     */
    public function getTenantEmailAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        $data = $this->data;
        return $data['admin_email'] ?? $data['email'] ?? null;
    }
    
    /**
     * Get the status from the data JSON if status field is null
     */
    public function getStatusAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        $data = $this->data;
        return $data['status'] ?? 'pending';
    }
    
    /**
     * Get the subscription plan from the data JSON if subscription_plan is null
     */
    public function getSubscriptionPlanAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        $data = $this->data;
        return $data['subscription_plan'] ?? 'basic';
    }
    
    /**
     * Set both the tenant_name field and the name in the data JSON
     */
    public function setTenantNameAttribute($value)
    {
        $this->attributes['tenant_name'] = $value;
        
        $data = $this->data;
        $data['name'] = $value;
        $this->data = $data;
    }
    
    /**
     * Set both the tenant_email field and the admin_email in the data JSON
     */
    public function setTenantEmailAttribute($value)
    {
        $this->attributes['tenant_email'] = $value;
        
        $data = $this->data;
        $data['admin_email'] = $value;
        $this->data = $data;
    }
    
    /**
     * Set both the status field and the status in the data JSON
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value;
        
        $data = $this->data;
        $data['status'] = $value;
        $this->data = $data;
    }
    
    /**
     * Set both the subscription_plan field and the subscription_plan in the data JSON
     */
    public function setSubscriptionPlanAttribute($value)
    {
        $this->attributes['subscription_plan'] = $value;
        
        $data = $this->data;
        $data['subscription_plan'] = $value;
        $this->data = $data;
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'data',
            'tenant_name',
            'tenant_email',
            'status',
            'subscription_plan',
        ];
    }

    public static function createWithData($subdomain, $data)
    {
        return static::create([
            'id' => $subdomain,
            'data' => json_encode([
                'name' => $data['name'],
                'admin_name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => bcrypt($data['password'])
            ])
        ]);
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * Get the tenant database associated with this tenant.
     */
    public function tenantDatabase()
    {
        return $this->hasOne(TenantDatabase::class, 'tenant_id', 'id');
    }
}