<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentSelfLowAttendance extends Mailable
{
    use Queueable, SerializesModels;

    public $subject_name, $subject_code, $total_hours, $present_count;
    /**
     * Create a new message instance.
     *
     * @return void
     */
     public function __construct($subject_name, $total_hours, $present_count)
     {
         $this->subject_name = $subject_name;
         $this->total_hours = $total_hours;
         $this->present_count = $present_count;
     }

    /**
     * Build the message.
     *
     * @return $this
     */
     public function build()
     {
         return $this->subject('Low attendance notification')->markdown('emails.studentselflowattendance');
     }
}
