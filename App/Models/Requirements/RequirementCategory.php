<?php

namespace App\Models\Requirements;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementCategory extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    
    protected $table = 'requirement_categories';

    protected $fillable = [
        'name',
        'description',
        'tenant_id'
    ];

    // Force tenant connection
    public function getConnectionName()
    {
        return 'tenant';
    }

    public function requirements()
    {
        return $this->hasMany(Requirement::class, 'category_id')->on($this->getConnectionName());
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