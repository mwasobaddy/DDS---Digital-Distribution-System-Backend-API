<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DCDWelcomeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $dcd;
    public $password;
    public $referralCode;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $dcd, $password, $referralCode)
    {
        $this->user = $user;
        $this->dcd = $dcd;
        $this->password = $password;
        $this->referralCode = $referralCode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Daya - Your Digital Content Distributor Account is Ready!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.dcd-welcome',
            with: [
                'user' => $this->user,
                'dcd' => $this->dcd,
                'password' => $this->password,
                'referralCode' => $this->referralCode,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}