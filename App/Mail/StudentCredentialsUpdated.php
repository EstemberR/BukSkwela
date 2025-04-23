<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Student\Student;

class StudentCredentialsUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $schoolName;
    public $loginUrl;
    public $updatedFields;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Student $student, array $updatedFields = [])
    {
        $this->student = $student;
        $this->updatedFields = $updatedFields;
        $this->schoolName = tenant('id') ?? 'BukSkwela';
        
        // Generate the login URL with the tenant domain
        $domain = tenant('id');
        $this->loginUrl = "http://{$domain}.localhost:8000/login";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Account Information Has Been Updated - ' . $this->schoolName)
                    ->view('emails.student-credentials-updated')
                    ->with([
                        'student' => $this->student,
                        'schoolName' => $this->schoolName,
                        'loginUrl' => $this->loginUrl,
                        'updatedFields' => $this->updatedFields
                    ]);
    }
} 