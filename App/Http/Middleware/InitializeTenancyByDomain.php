<?php

namespace App\Http\Middleware;

use Stancl\Tenancy\Middleware\InitializeTenancyByDomain as BaseMiddleware;

class InitializeTenancyByDomain extends BaseMiddleware
{
    // No need to override handle method unless you need custom logic
}