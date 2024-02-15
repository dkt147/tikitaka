<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMemberMail extends Mailable
{
    use Queueable, SerializesModels;
    public $invitee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invitee)
    {
        $this->invitee = $invitee;
    }

    public function build()
    {
        return $this->subject('Invitation')
            ->view('emails.invitation');
    }
}
