<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Staff\Staff;

class StaffRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $staff;
    public $schoolName;
    public $password;
    public $loginUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Staff $staff, string $password)
    {
        $this->staff = $staff;
        $this->password = $password;
        $this->schoolName = tenant('id') ?? 'BukSkwela';
        
        // Generate the login URL with the tenant domain
        $domain = tenant('id');
        $this->loginUrl = "http://{$domain}.localhost/login";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to ' . $this->schoolName . ' Staff')
                    ->view('emails.staff-registered')
                    ->with([
                        'staff' => $this->staff,
                        'schoolName' => $this->schoolName,
                        'password' => $this->password,
                        'loginUrl' => $this->loginUrl
                    ]);
    }
} 