<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Student\Student;

class StudentRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $schoolName;
    public $password;
    public $loginUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Student $student, string $password)
    {
        $this->student = $student;
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
        return $this->subject('Welcome to ' . $this->schoolName)
                    ->view('emails.student-registered')
                    ->with([
                        'student' => $this->student,
                        'schoolName' => $this->schoolName,
                        'password' => $this->password,
                        'loginUrl' => $this->loginUrl
                    ]);
    }
} 