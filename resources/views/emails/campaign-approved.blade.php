<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Campaign Has Been Approved</title>
</head>
<body>
    <h1>Congratulations! Your Campaign Has Been Approved</h1>

    <p>Dear {{ $campaign->client->name ?? 'Client' }},</p>

    <p>Your campaign "{{ $campaign->title }}" has been approved and is now active.</p>

    <h2>Campaign Details:</h2>
    <ul>
        <li><strong>Campaign ID:</strong> {{ $campaign->id }}</li>
        <li><strong>Name:</strong> {{ $campaign->title }}</li>
        <li><strong>Product URL:</strong> {{ $campaign->product_url }}</li>
        <li><strong>Rate per Scan:</strong> KES {{ number_format($campaign->rate_per_scan, 2) }}</li>
        <li><strong>Status:</strong> Active</li>
    </ul>

    <p>Your QR codes are now ready for distribution. Users can start scanning to earn rewards and drive traffic to your product.</p>

    <p>You can track campaign performance through our dashboard.</p>

    <p>Best regards,<br>
    The DDS Team</p>
</body>
</html>