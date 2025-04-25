<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionUpgrade extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql'; // This ensures we use the central database

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tenant_id',
        'from_plan',
        'to_plan',
        'payment_method',
        'transaction_id',
        'receipt_number',
        'reference_number',
        'amount',
        'status',
        'processed_at',
        'processed_by',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'processed_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    /**
     * Get the tenant that owns the subscription upgrade.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
} 