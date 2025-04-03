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
        'data'
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

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'data',
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
}
