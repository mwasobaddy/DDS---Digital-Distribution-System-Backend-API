<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DAWelcomeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $da;
    public $password;
    public $referralCode;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $da, $password, $referralCode)
    {
        $this->user = $user;
        $this->da = $da;
        $this->password = $password;
        $this->referralCode = $referralCode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Daya - Your Digital Ambassador Account is Ready!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.da-welcome',
            with: [
                'user' => $this->user,
                'da' => $this->da,
                'password' => $this->password,
                'referralCode' => $this->referralCode,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
