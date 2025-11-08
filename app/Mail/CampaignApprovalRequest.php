<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignApprovalRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $campaign;
    public $client;
    public $admin;
    public $approveUrl;
    public $rejectUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $campaign, $client, $admin, $approveUrl, $rejectUrl)
    {
        $this->user = $user;
        $this->campaign = $campaign;
        $this->client = $client;
        $this->admin = $admin;
        $this->approveUrl = $approveUrl;
        $this->rejectUrl = $rejectUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.admin.campaign-approval-request')
                    ->subject('New Campaign Approval Required')
                    ->with([
                        'user' => $this->user,
                        'campaign' => $this->campaign,
                        'client' => $this->client,
                        'admin' => $this->admin,
                        'approveUrl' => $this->approveUrl,
                        'rejectUrl' => $this->rejectUrl,
                    ]);
    }
}