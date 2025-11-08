<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $isExistingUser ? 'New Campaign Created Successfully' : 'Welcome to Daya - Campaign Created' }}</title>
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
            <h1>{{ $isExistingUser ? 'New Campaign Created!' : 'Welcome to Daya!' }}</h1>
            <p>{{ $isExistingUser ? 'Your campaign is ready for review' : 'Your account and campaign have been created' }}</p>
        </div>

        <div class="content">
            <p>Hello {{ $user->name }},</p>

            @if($isExistingUser)
                <p>Your new campaign has been created successfully and is now pending admin approval.</p>
            @else
                <p>Welcome to Daya! Your account and first campaign have been created successfully.</p>
            @endif

            <div class="campaign-details">
                <h3>Campaign Details</h3>
                <p><strong>Campaign Name:</strong> {{ $campaign->title }}</p>
                <p><strong>Campaign ID:</strong> {{ $campaign->id }}</p>
                <p><strong>Budget:</strong> KES {{ number_format($campaign->budget) }}</p>
                <p><strong>Status:</strong> {{ $campaign->status }}</p>
                <p><strong>Product URL:</strong> {{ $campaign->product_url }}</p>
                @if($campaign->start_date)
                    <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($campaign->start_date)->format('M d, Y') }}</p>
                @endif
                @if($campaign->end_date)
                    <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($campaign->end_date)->format('M d, Y') }}</p>
                @endif
            </div>

            <div class="info">
                <p><strong>Next Steps:</strong></p>
                <p>Your campaign is now pending admin approval. You will receive an email notification once your campaign is approved or if any changes are required.</p>
            </div>

            @if(!$isExistingUser)
                <p>As a new user, you should have received a separate email with your login credentials. Please use those to access your dashboard and track your campaign progress.</p>
            @endif

            <p>Thank you for choosing Daya for your digital distribution needs!</p>

            <p>Best regards,<br>
            The Daya Team</p>
        </div>

        <div class="footer">
            <p>Daya - Digital Distribution Platform</p>
            <p>This email was sent to {{ $user->email }}</p>
        </div>
    </div>
</body>
</html>