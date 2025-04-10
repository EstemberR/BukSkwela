<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        'dashboard_layout',
        'layout_config',
        'additional_settings'
    ];

    protected $casts = [
        'dark_mode' => 'boolean',
        'additional_settings' => 'array',
    ];
    
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Add a saving event to ensure tenant_id is always set
        static::saving(function ($model) {
            if (empty($model->tenant_id)) {
                // Try to get tenant from the global function
                if (function_exists('tenant')) {
                    try {
                        $tenant = tenant();
                        if ($tenant && isset($tenant->id)) {
                            $model->tenant_id = $tenant->id;
                            Log::info('UserSettings auto-set tenant_id from global tenant()', [
                                'tenant_id' => $model->tenant_id,
                                'user_id' => $model->user_id
                            ]);
                        } else {
                            // If tenant() doesn't have a valid ID, attempt to get it from request header
                            $tenantId = request()->header('X-Tenant-ID');
                            if (!empty($tenantId)) {
                                $model->tenant_id = $tenantId;
                                Log::info('UserSettings auto-set tenant_id from request header', [
                                    'tenant_id' => $model->tenant_id,
                                    'user_id' => $model->user_id
                                ]);
                            } else {
                                Log::warning('Tenant function exists but returned null or invalid value, and no header available');
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Error getting tenant in UserSettings saving event: ' . $e->getMessage());
                    }
                } else {
                    Log::warning('Tenant function does not exist in UserSettings saving event');
                }
            } else {
                Log::info('UserSettings already has tenant_id set', ['tenant_id' => $model->tenant_id]);
            }
            
            // Ensure dashboard_layout has a valid value if set
            if (!empty($model->dashboard_layout) && !in_array($model->dashboard_layout, ['standard', 'compact', 'modern'])) {
                $model->dashboard_layout = 'standard';
                Log::info('Fixed invalid dashboard_layout value', [
                    'old_value' => $model->getOriginal('dashboard_layout') ?? 'null',
                    'new_value' => 'standard'
                ]);
            }
            
            // Log the final model state before saving
            Log::info('UserSettings saving event', [
                'tenant_id' => $model->tenant_id,
                'user_id' => $model->user_id,
                'dashboard_layout' => $model->dashboard_layout ?? 'not set',
                'has_layout_config' => !empty($model->layout_config)
            ]);
        });
    }

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
        Log::info('Using UserSettings::forTenant scope', ['tenant_id' => $tenantId]);
        
        // Handle both cases where tenant_id might be set or null (for backward compatibility)
        if (!empty($tenantId)) {
            return $query->where(function($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId)
                      ->orWhereNull('tenant_id');
            });
        }
        
        return $query;
    }

    /**
     * Get dashboard layout with fallback to standard
     *
     * @return string
     */
    public function getDashboardLayoutAttribute($value)
    {
        // Ensure we always return a valid layout value
        if (empty($value) || !in_array($value, ['standard', 'compact', 'modern'])) {
            return 'standard';
        }
        
        return $value;
    }
}
