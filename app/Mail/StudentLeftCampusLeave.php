<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Leave;

class StudentLeftCampusLeave extends Mailable
{
    use Queueable, SerializesModels;

    public $out_date, $in_date, $visit_to, $reason;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Leave $leave)
    {
        $this->out_date = $leave->out_date;
        $this->in_date = $leave->in_date;
        $this->visit_to = $leave->visit_to;
        $this->reason = $leave->reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Student campus left notification')->markdown('emails.studentleftcampusleave');
    }
}
