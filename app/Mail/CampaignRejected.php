<?php

namespace App\Mail;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignRejected extends Mailable
{
    use Queueable, SerializesModels;

    public Campaign $campaign;
    public string $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(Campaign $campaign, string $reason = '')
    {
        $this->campaign = $campaign;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Campaign Update - DDS',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign-rejected',
            with: [
                'campaign' => $this->campaign,
                'reason' => $this->reason,
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