<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class TenantUserCredential extends Authenticatable
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'tenant';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'user_type',
        'user_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the related user based on user_type
     */
    public function getUser()
    {
        switch ($this->user_type) {
            case 'admin':
                return TenantAdmin::on('tenant')->find($this->user_id);
            case 'staff':
                // Assuming you have a Staff model
                return Staff::on('tenant')->find($this->user_id);
            case 'student':
                // Assuming you have a Student model
                return Student::on('tenant')->find($this->user_id);
            default:
                return null;
        }
    }
}
