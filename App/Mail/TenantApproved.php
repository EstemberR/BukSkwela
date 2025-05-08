<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantApproved extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The tenant instance.
     *
     * @var \App\Models\Tenant
     */
    public $tenant;

    /**
     * The login URL for the tenant.
     *
     * @var string
     */
    public $loginUrl;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
        
        // Generate the tenant login URL
        $tenantDomain = config('app.url');
        
        // If this is a multi-tenant setup using domains, use tenant's domain if available
        if ($tenant->domains && $tenant->domains->count() > 0) {
            $tenantDomain = $tenant->domains->first()->domain;
            // Make sure it has the proper protocol
            if (!preg_match('#^https?://#', $tenantDomain)) {
                $tenantDomain = 'https://' . $tenantDomain;
            }
        } else {
            // Otherwise use the subdomain format
            $baseUrl = config('app.url');
            $parsedUrl = parse_url($baseUrl);
            $scheme = $parsedUrl['scheme'] ?? 'https';
            $host = $parsedUrl['host'] ?? 'bukskwela.com';
            
            if (isset($parsedUrl['port'])) {
                $tenantDomain = "$scheme://{$tenant->id}.$host:{$parsedUrl['port']}";
            } else {
                $tenantDomain = "$scheme://{$tenant->id}.$host";
            }
        }
        
        $this->loginUrl = $tenantDomain . '/admin';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Tenant Account Has Been Approved')
                    ->view('emails.ApprovedTenant');
    }
} 