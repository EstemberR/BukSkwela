<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Staff\Staff;

class StaffCredentialsUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $staff;
    public $schoolName;
    public $loginUrl;
    public $updatedFields;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Staff $staff, array $updatedFields = [])
    {
        $this->staff = $staff;
        $this->updatedFields = $updatedFields;
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
        return $this->subject('Your Account Information Has Been Updated - ' . $this->schoolName)
                    ->view('emails.staff-credentials-updated')
                    ->with([
                        'staff' => $this->staff,
                        'schoolName' => $this->schoolName,
                        'loginUrl' => $this->loginUrl,
                        'updatedFields' => $this->updatedFields
                    ]);
    }
} 