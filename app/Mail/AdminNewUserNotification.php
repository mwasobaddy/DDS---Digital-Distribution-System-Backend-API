<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewUserNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $userType;
    public $user;
    public $userModel;
    public $admin;

    /**
     * Create a new message instance.
     */
    public function __construct(string $userType, $user, $userModel, $admin)
    {
        $this->userType = $userType;
        $this->user = $user;
        $this->userModel = $userModel;
        $this->admin = $admin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New {$this->userType} Registration - DDS System",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.new-user-notification',
            with: [
                'userType' => $this->userType,
                'user' => $this->user,
                'userModel' => $this->userModel,
                'admin' => $this->admin,
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
