<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignCreatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $campaign;
    public $isExistingUser;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $campaign, $isExistingUser = false)
    {
        $this->user = $user;
        $this->campaign = $campaign;
        $this->isExistingUser = $isExistingUser;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->isExistingUser ? 'New Campaign Created Successfully' : 'Welcome to Daya - Campaign Created';
        
        return $this->view('emails.campaign-created')
                    ->subject($subject)
                    ->with([
                        'user' => $this->user,
                        'campaign' => $this->campaign,
                        'isExistingUser' => $this->isExistingUser,
                    ]);
    }
}