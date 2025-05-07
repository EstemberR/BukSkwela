<?php

namespace App\Helpers;

/**
 * Return the tenancy manager instance
 * 
 * @return \Stancl\Tenancy\Tenancy
 */
function tenancy() 
{
    return app(\Stancl\Tenancy\Tenancy::class);
} 