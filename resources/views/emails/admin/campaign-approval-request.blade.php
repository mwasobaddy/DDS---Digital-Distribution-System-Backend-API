<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Campaign Approval Required</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #ffc107; color: #212529; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .campaign-details { background-color: white; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107; }
        .client-details { background-color: #e8f4f8; padding: 15px; margin: 15px 0; border-left: 4px solid #007bff; }
        .actions { text-align: center; margin: 20px 0; }
        .btn { display: inline-block; padding: 10px 20px; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
        .btn-approve { background-color: #28a745; }
        .btn-reject { background-color: #dc3545; }
        .footer { text-align: center; font-size: 12px; color: #666; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Campaign Pending Approval</h1>
            <p>Admin Action Required</p>
        </div>

        <div class="content">
            <p>Hello {{ $admin->name }},</p>

            <p>A new campaign has been submitted and requires your approval:</p>

            <div class="client-details">
                <h3>Client Information</h3>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Phone:</strong> {{ $user->phone }}</p>
                @if($client)
                    <p><strong>Company:</strong> {{ $client->company_name }}</p>
                    <p><strong>Account Type:</strong> {{ $client->account_type }}</p>
                    <p><strong>Country:</strong> {{ $client->country }}</p>
                @endif
            </div>

            <div class="campaign-details">
                <h3>Campaign Details</h3>
                <p><strong>Campaign Name:</strong> {{ $campaign->title }}</p>
                <p><strong>Campaign ID:</strong> {{ $campaign->id }}</p>
                <p><strong>Type:</strong> {{ $campaign->campaign_type }}</p>
                <p><strong>Budget:</strong> KES {{ number_format($campaign->budget) }}</p>
                <p><strong>Product URL:</strong> <a href="{{ $campaign->product_url }}" target="_blank">{{ $campaign->product_url }}</a></p>
                @if($campaign->explainer_video_url)
                    <p><strong>Explainer Video:</strong> <a href="{{ $campaign->explainer_video_url }}" target="_blank">{{ $campaign->explainer_video_url }}</a></p>
                @endif
                @if($campaign->objective)
                    <p><strong>Objective:</strong> {{ $campaign->objective }}</p>
                @endif
                @if($campaign->start_date)
                    <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($campaign->start_date)->format('M d, Y') }}</p>
                @endif
                @if($campaign->end_date)
                    <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($campaign->end_date)->format('M d, Y') }}</p>
                @endif
                <p><strong>Submission Date:</strong> {{ $campaign->created_at->format('M d, Y H:i:s') }}</p>
            </div>

            <div class="actions">
                <p><strong>Action Required:</strong></p>
                <a href="{{ $approveUrl }}" class="btn btn-approve">Approve Campaign</a>
                <a href="{{ $rejectUrl }}" class="btn btn-reject">Reject Campaign</a>
            </div>

            <p>Please review the campaign details carefully and take appropriate action.</p>

            <p>This is an automated notification from the Daya admin system.</p>
        </div>

        <div class="footer">
            <p>Daya - Digital Distribution Platform (Admin System)</p>
            <p>This email was sent to {{ $admin->email }}</p>
        </div>
    </div>
</body>
</html>