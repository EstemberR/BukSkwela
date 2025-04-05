<?php

namespace App\Models\Requirements;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'tenant_id'
    ];

    public function requirements()
    {
        return $this->hasMany(Requirement::class, 'category_id');
    }
} 