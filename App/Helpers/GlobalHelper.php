<?php

if (!function_exists('tenancy')) {
    /**
     * Return the tenancy manager instance
     * 
     * @return \Stancl\Tenancy\Tenancy
     */
    function tenancy() 
    {
        return app('tenancy.helper');
    }
} 