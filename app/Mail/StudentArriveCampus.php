<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class StudentArriveCampus extends Mailable
{
    use Queueable, SerializesModels;

    public $time;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->time = $time;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Student entered in campus notification')->markdown('emails.studentarrivecampus');
    }
}
