<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Campaign Update - Requires Changes</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .campaign-details { background-color: white; padding: 15px; margin: 15px 0; border-left: 4px solid #dc3545; }
        .rejection-reason { background-color: #f8d7da; padding: 15px; margin: 15px 0; border-left: 4px solid #dc3545; color: #721c24; }
        .info { background-color: #fff3cd; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107; color: #856404; }
        .footer { text-align: center; font-size: 12px; color: #666; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Campaign Update - Requires Changes</h1>
            <p>Your campaign needs some modifications before approval</p>
        </div>

        <div class="content">
            <p>Hello {{ $campaign->client->name ?? 'Client' }},</p>

            <p>Thank you for submitting your campaign. After careful review, we found that some changes are needed before we can approve it.</p>

            @if($reason)
                <div class="rejection-reason">
                    <h3>Feedback & Required Changes:</h3>
                    <p>{{ $reason }}</p>
                </div>
            @endif

            <div class="campaign-details">
                <h3>Campaign Details</h3>
                <p><strong>Campaign ID:</strong> {{ $campaign->id }}</p>
                <p><strong>Name:</strong> {{ $campaign->title }}</p>
                <p><strong>Product URL:</strong> {{ $campaign->product_url }}</p>
                <p><strong>Status:</strong> Requires Changes</p>
                @if($campaign->start_date)
                    <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($campaign->start_date)->format('M d, Y') }}</p>
                @endif
                @if($campaign->end_date)
                    <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($campaign->end_date)->format('M d, Y') }}</p>
                @endif
            </div>

            <div class="info">
                <p><strong>Next Steps:</strong></p>
                <p>Please review the feedback above and make the necessary changes to your campaign. Once updated, you can resubmit it for approval.</p>
                <p>If you have any questions or need clarification, please don't hesitate to contact our support team.</p>
            </div>

            <p>We appreciate your understanding and look forward to approving your campaign once the changes are made.</p>

            <p>Best regards,<br>
            The Daya Team</p>
        </div>

        <div class="footer">
            <p>Daya - Digital Distribution Platform</p>
            <p>This email was sent to {{ $campaign->client->email ?? 'client email' }}</p>
        </div>
    </div>
</body>
</html>