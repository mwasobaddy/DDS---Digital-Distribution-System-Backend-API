<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Campaign Update</title>
</head>
<body>
    <h1>Campaign Update</h1>

    <p>Dear {{ $campaign->client->name ?? 'Client' }},</p>

    <p>We regret to inform you that your campaign "{{ $campaign->title }}" has been reviewed and requires some changes before it can be approved.</p>

    @if($reason)
        <h2>Reason for Rejection:</h2>
        <p>{{ $reason }}</p>
    @endif

    <h2>Campaign Details:</h2>
    <ul>
        <li><strong>Campaign ID:</strong> {{ $campaign->id }}</li>
        <li><strong>Name:</strong> {{ $campaign->title }}</li>
        <li><strong>Product URL:</strong> {{ $campaign->product_url }}</li>
    </ul>

    <p>Please review the feedback and resubmit your campaign with the necessary changes. You can edit your campaign details and submit for approval again.</p>

    <p>If you have any questions, please don't hesitate to contact our support team.</p>

    <p>Best regards,<br>
    The DDS Team</p>
</body>
</html>