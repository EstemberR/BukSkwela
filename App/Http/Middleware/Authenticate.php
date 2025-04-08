<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            try {
                $hasTenant = function_exists('tenant') && tenant() !== null;
                return $hasTenant ? route('tenant.login') : '/login';
            } catch (\Exception $e) {
                Log::error('Error in authentication redirect', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return '/login';
            }
        }
    }
}
