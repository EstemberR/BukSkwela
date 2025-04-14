<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class Student extends Authenticatable
{
    use Notifiable;

    /**
     * Get the database connection to use.
     *
     * @return string
     */
    public function getConnectionName()
    {
        // First try to get from config (set by service provider)
        if (config('app.tenant_db')) {
            $tenantDb = config('app.tenant_db');
            config(['database.connections.tenant.database' => $tenantDb]);
            DB::purge('tenant');
            DB::reconnect('tenant');
            return 'tenant';
        }
        
        // Then try to get from session
        if (session('tenant_db')) {
            $tenantDb = session('tenant_db');
            config(['database.connections.tenant.database' => $tenantDb]);
            DB::purge('tenant');
            DB::reconnect('tenant');
            return 'tenant';
        }
        
        // Final fallback: extract from host
        $host = Request::getHost();
        $parts = explode('.', $host);
        $subdomain = null;
        
        // For localhost with port
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
            if (count($parts) >= 2) {
                $subdomain = $parts[0];
            }
        } else if (count($parts) > 2) {
            $subdomain = $parts[0];
        }
        
        if ($subdomain && $subdomain != 'www') {
            $tenantDb = "tenant_{$subdomain}";
            config(['database.connections.tenant.database' => $tenantDb]);
            DB::purge('tenant');
            DB::reconnect('tenant');
            return 'tenant';
        }
        
        return 'tenant';
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'students';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'student_id',
        'course_id',
        'status',
        'profile_photo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the course that the student belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
} 