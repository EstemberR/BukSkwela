<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Tenant;

class TenantRegistrationPending extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;
    public $password;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Tenant $tenant, string $password)
    {
        $this->tenant = $tenant;
        $this->password = $password;
        $this->loginUrl = "http://{$tenant->id}." . env('CENTRAL_DOMAIN') . ":8000/login";
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your BukSkwela Registration is Pending Approval')
                    ->view('emails.tenant-registration-pending');
    }
} 