<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Staff\Staff;
use App\Traits\HasTenantConnection;

class Department extends Model
{
    use HasFactory, HasTenantConnection;

    protected $connection = 'tenant';
    
    protected $table = 'departments';

    protected $fillable = [
        'id',
        'name',
        'code',
        'description',
        'tenant_id',
        'status',
        'created_at',
        'updated_at'
    ];

    // Force tenant connection
    public function getConnectionName()
    {
        return 'tenant';
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
    
    // Boot method to ensure tenant_id is set
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (tenant()) {
                $model->tenant_id = tenant('id');
            }
        });
    }
} 