<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class TenantDatabase extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tenant_id',
        'database_name',
        'database_username',
        'database_password',
        'database_host',
        'database_port'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'database_password',
    ];
    
    /**
     * Get the tenant that owns the database.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }
    
    /**
     * Get the database connection configuration for this tenant
     * 
     * @return array
     */
    public function getConnectionConfig(): array
    {
        return [
            'driver' => 'mysql',
            'host' => $this->database_host,
            'port' => $this->database_port,
            'database' => $this->database_name,
            'username' => $this->database_username,
            'password' => $this->database_password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ];
    }
    
    /**
     * Encrypt the password when it is set
     *
     * @param string $value
     * @return void
     */
    public function setDatabasePasswordAttribute($value)
    {
        $this->attributes['database_password'] = Crypt::encryptString($value);
    }
    
    /**
     * Decrypt the password when it is accessed
     *
     * @param string $value
     * @return string
     */
    public function getDatabasePasswordAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // If the value is not encrypted (for existing records),
            // return it as is to maintain backward compatibility
            return $value;
        }
    }
}
