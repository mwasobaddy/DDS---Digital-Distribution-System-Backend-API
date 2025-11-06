<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Campaign Invoice - {{ $invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .invoice-details { margin-bottom: 30px; }
        .invoice-details table { width: 100%; border-collapse: collapse; }
        .invoice-details td { padding: 8px; border: 1px solid #ddd; }
        .campaign-info { margin-bottom: 30px; }
        .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DDS Campaign Invoice</h1>
        <h2>{{ $invoice_number }}</h2>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td><strong>Invoice Date:</strong></td>
                <td>{{ $generated_at->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <td><strong>Client:</strong></td>
                <td>{{ $client->company_name ?? $client->name }}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>{{ $client->email }}</td>
            </tr>
        </table>
    </div>

    <div class="campaign-info">
        <h3>Campaign Details</h3>
        <p><strong>Campaign ID:</strong> {{ $campaign->id }}</p>
        <p><strong>Title:</strong> {{ $campaign->title }}</p>
        <p><strong>Description:</strong> {{ $campaign->description }}</p>
        <p><strong>Budget:</strong> ${{ number_format($campaign->budget, 2) }}</p>
        <p><strong>Rate per Scan:</strong> ${{ number_format($campaign->rate_per_scan, 2) }}</p>
        <p><strong>Target Counties:</strong> {{ implode(', ', $campaign->target_counties ?? []) }}</p>
    </div>

    <div class="total">
        <p>Total Amount Due: ${{ number_format($amount, 2) }}</p>
    </div>

    <div style="margin-top: 50px; text-align: center; font-size: 12px; color: #666;">
        <p>Thank you for choosing DDS. Please remit payment within 30 days.</p>
        <p>Payment instructions will be provided separately.</p>
    </div>
</body>
</html>