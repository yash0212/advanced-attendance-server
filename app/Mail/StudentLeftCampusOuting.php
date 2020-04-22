<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Outing;

class StudentLeftCampusOuting extends Mailable
{
    use Queueable, SerializesModels;

    public $date, $out_time, $in_time, $visit_to, $reason;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Outing $outing)
    {
        $this->date = $outing->date;
        $this->out_time = $outing->out_time;
        $this->in_time = $outing->in_time;
        $this->visit_to = $outing->visit_to;
        $this->reason = $outing->reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Student campus left notification')->markdown('emails.studentleftcampusouting');
    }
}
