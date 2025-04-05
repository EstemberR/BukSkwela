<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Staff\Staff;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'tenant_id',
        'status'
    ];

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
} 