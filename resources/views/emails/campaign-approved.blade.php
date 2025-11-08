<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Campaign Has Been Approved</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .campaign-details { background-color: white; padding: 15px; margin: 15px 0; border-left: 4px solid #28a745; }
        .info { background-color: #d1ecf1; padding: 15px; margin: 15px 0; border-left: 4px solid #17a2b8; color: #0c5460; }
        .footer { text-align: center; font-size: 12px; color: #666; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Congratulations! Your Campaign Has Been Approved</h1>
            <p>Your campaign is now live and ready for distribution</p>
        </div>

        <div class="content">
            <p>Hello {{ $campaign->client->name ?? 'Client' }},</p>

            <p>Great news! Your campaign has been reviewed and approved. It's now active and ready to start generating results.</p>

            <div class="campaign-details">
                <h3>Campaign Details</h3>
                <p><strong>Campaign ID:</strong> {{ $campaign->id }}</p>
                <p><strong>Name:</strong> {{ $campaign->title }}</p>
                <p><strong>Product URL:</strong> {{ $campaign->product_url }}</p>
                <p><strong>Rate per Scan:</strong> KES {{ number_format($campaign->rate_per_scan, 2) }}</p>
                <p><strong>Status:</strong> Active</p>
                @if($campaign->start_date)
                    <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($campaign->start_date)->format('M d, Y') }}</p>
                @endif
                @if($campaign->end_date)
                    <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($campaign->end_date)->format('M d, Y') }}</p>
                @endif
            </div>

            <div class="info">
                <p><strong>What's Next:</strong></p>
                <p>Your QR codes are now ready for distribution. Digital Ambassadors can start scanning to earn rewards and drive traffic to your product.</p>
                <p>You can track campaign performance and analytics through your dashboard.</p>
            </div>

            <p>Thank you for choosing Daya for your digital distribution needs!</p>

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