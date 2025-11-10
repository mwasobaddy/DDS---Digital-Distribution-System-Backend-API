<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Daya - Your Digital Content Distributor Account is Ready!</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
        .header p { margin: 10px 0 0 0; font-size: 16px; opacity: 0.9; }
        .content { padding: 40px 30px; }
        .welcome-message { font-size: 18px; color: #7c3aed; font-weight: 600; margin-bottom: 20px; }
        .account-details { background-color: #f3f0ff; border: 1px solid #ddd6fe; border-radius: 8px; padding: 25px; margin: 25px 0; }
        .account-details h3 { margin-top: 0; color: #581c87; font-size: 20px; }
        .detail-row { display: flex; margin-bottom: 12px; }
        .detail-label { font-weight: 600; color: #374151; width: 140px; flex-shrink: 0; }
        .detail-value { color: #111827; word-break: break-all; }
        .password-warning { background-color: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 15px; margin: 20px 0; }
        .password-warning strong { color: #92400e; }
        .business-info { background-color: #fef7ff; border-left: 4px solid #7c3aed; padding: 20px; margin: 25px 0; }
        .business-info h3 { margin-top: 0; color: #6b21a8; }
        .earning-info { background-color: #f0fdf4; border-left: 4px solid #059669; padding: 20px; margin: 25px 0; }
        .earning-info h3 { margin-top: 0; color: #065f46; }
        .next-steps { background-color: #f3f4f6; border-radius: 8px; padding: 25px; margin: 25px 0; }
        .next-steps h3 { margin-top: 0; color: #111827; }
        .steps-list { list-style: none; padding: 0; }
        .steps-list li { margin-bottom: 12px; padding-left: 25px; position: relative; }
        .steps-list li:before { content: '✓'; position: absolute; left: 0; color: #7c3aed; font-weight: bold; }
        .referral-section { background-color: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 25px; margin: 25px 0; }
        .referral-section h3 { margin-top: 0; color: #0c4a6e; }
        .referral-code { background-color: #e0f2fe; border: 2px dashed #0284c7; border-radius: 6px; padding: 15px; text-align: center; margin: 15px 0; }
        .referral-code strong { font-size: 18px; color: #0369a1; letter-spacing: 2px; }
        .support-section { text-align: center; background-color: #f9fafb; padding: 25px; margin: 25px 0; border-radius: 8px; }
        .support-section h3 { margin-top: 0; color: #374151; }
        .footer { background-color: #1f2937; color: white; padding: 30px; text-align: center; }
        .footer p { margin: 0; font-size: 14px; }
        .footer a { color: #a855f7; text-decoration: none; }
        .button { display: inline-block; background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); color: white; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-weight: 600; margin: 10px 0; }
        .button:hover { background: linear-gradient(135deg, #6d28d9 0%, #9333ea 100%); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Daya!</h1>
            <p>Your Digital Content Distributor Account is Ready</p>
        </div>

        <div class="content">
            <div class="welcome-message">
                Congratulations {{ $user->name }}!
            </div>

            <p>You've successfully joined Africa's leading digital distribution network as a Digital Content Distributor. Your account has been created and is ready to start earning through content distribution.</p>

            <div class="account-details">
                <h3>Your Account Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Full Name:</span>
                    <span class="detail-value">{{ $user->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $user->phone }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">DCD ID:</span>
                    <span class="detail-value">{{ $dcd->id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Business:</span>
                    <span class="detail-value">{{ $dcd->business_name ?: 'Not specified' }}</span>
                </div>
            </div>

            <div class="password-warning">
                <strong>Important:</strong> Your temporary password is: <strong>{{ $password }}</strong><br>
                Please log in and change your password immediately for security.
            </div>

            @if($dcd->business_name)
            <div class="business-info">
                <h3>Your Business Information</h3>
                <p><strong>Business Name:</strong> {{ $dcd->business_name }}</p>
                @if($dcd->business_types)
                <p><strong>Business Type:</strong> {{ is_array($dcd->business_types) ? implode(', ', $dcd->business_types) : $dcd->business_types }}</p>
                @endif
                @if($dcd->business_address)
                <p><strong>Address:</strong> {{ $dcd->business_address }}</p>
                @endif
            </div>
            @endif

            <div class="earning-info">
                <h3>How You'll Earn</h3>
                <p>As a Digital Content Distributor, you'll earn by sharing digital content at your business location. Here's how it works:</p>
                <div class="earning-grid">
                    <div class="earning-item">
                        <h4>Content Sharing</h4>
                        <p>Display digital content on screens or devices at your business location</p>
                    </div>
                    <div class="earning-item">
                        <h4>Earn Per Scan</h4>
                        <p>Get paid each time customers scan QR codes to access content or offers</p>
                    </div>
                    <div class="earning-item">
                        <h4>Referral Bonuses</h4>
                        <p>Earn additional income when customers you refer become active users</p>
                    </div>
                </div>
            </div>

            <div class="next-steps">
                <h3>Next Steps to Start Earning</h3>
                <ol class="steps-list">
                    <li>Log in to your account using the temporary password above</li>
                    <li>Change your password and complete your profile</li>
                    <li>Download and print your unique QR code</li>
                    <li>Display the QR code prominently at your business</li>
                    <li>Start earning when customers scan and engage with content</li>
                </ol>
            </div>

            @if($referralCode)
            <div class="referral-section">
                <h3>Your Referral Code</h3>
                <p>Use this code to refer other businesses to join the Daya network:</p>
                <div class="referral-code">
                    <strong>{{ $referralCode }}</strong>
                </div>
                <p>Share this code with other business owners and earn bonuses when they join!</p>
            </div>
            @endif

            <div class="support-section">
                <h3>Need Help?</h3>
                <p>Our support team is here to help you get started. Contact us anytime:</p>
                <p><strong>Email:</strong> support@daya.africa<br>
                <strong>Phone:</strong> +254 700 000 000</p>
                <a href="https://daya.africa/support" class="button">Visit Support Center</a>
            </div>
        </div>

        <div class="footer">
            <p>Welcome to the Daya family! We're excited to have you as part of our growing network.</p>
            <p><a href="https://daya.africa">Visit Daya Website</a> | <a href="https://daya.africa/login">Login to Your Account</a></p>
            <p>&copy; 2025 Daya. All rights reserved.</p>
        </div>
    </div>
</body>
</html>