<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'tenant_id',
        'dark_mode',
        'card_style',
        'font_family',
        'font_size',
        'additional_settings'
    ];

    protected $casts = [
        'dark_mode' => 'boolean',
        'additional_settings' => 'array',
    ];

    /**
     * Get the user that owns the settings
     */
    public function user()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include settings for a specific tenant.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $tenantId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
