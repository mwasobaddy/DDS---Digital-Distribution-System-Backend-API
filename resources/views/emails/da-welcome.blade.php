<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Daya - Your Digital Ambassador Journey Begins!</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
        .header p { margin: 10px 0 0 0; font-size: 16px; opacity: 0.9; }
        .content { padding: 40px 30px; }
        .welcome-message { font-size: 18px; color: #059669; font-weight: 600; margin-bottom: 20px; }
        .account-details { background-color: #f0fdf4; border: 1px solid #dcfce7; border-radius: 8px; padding: 25px; margin: 25px 0; }
        .account-details h3 { margin-top: 0; color: #166534; font-size: 20px; }
        .detail-row { display: flex; margin-bottom: 12px; }
        .detail-label { font-weight: 600; color: #374151; width: 140px; flex-shrink: 0; }
        .detail-value { color: #111827; word-break: break-all; }
        .password-warning { background-color: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 15px; margin: 20px 0; }
        .password-warning strong { color: #92400e; }
        .earning-info { background-color: #ecfdf5; border-left: 4px solid #059669; padding: 20px; margin: 25px 0; }
        .earning-info h3 { margin-top: 0; color: #065f46; }
        .earning-grid { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px; }
        .earning-item { flex: 1; min-width: 200px; background: white; padding: 15px; border-radius: 6px; border: 1px solid #d1fae5; }
        .earning-item h4 { margin: 0 0 8px 0; color: #059669; font-size: 16px; }
        .earning-item p { margin: 0; font-size: 14px; color: #374151; }
        .next-steps { background-color: #f3f4f6; border-radius: 8px; padding: 25px; margin: 25px 0; }
        .next-steps h3 { margin-top: 0; color: #111827; }
        .steps-list { list-style: none; padding: 0; }
        .steps-list li { margin-bottom: 12px; padding-left: 25px; position: relative; }
        .steps-list li:before { content: '✓'; position: absolute; left: 0; color: #059669; font-weight: bold; }
        .referral-section { background-color: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 25px; margin: 25px 0; }
        .referral-section h3 { margin-top: 0; color: #0c4a6e; }
        .referral-code { background-color: #e0f2fe; border: 2px dashed #0284c7; border-radius: 6px; padding: 15px; text-align: center; margin: 15px 0; }
        .referral-code strong { font-size: 18px; color: #0369a1; letter-spacing: 2px; }
        .support-section { text-align: center; background-color: #f9fafb; padding: 25px; margin: 25px 0; border-radius: 8px; }
        .support-section h3 { margin-top: 0; color: #374151; }
        .footer { background-color: #1f2937; color: white; padding: 30px; text-align: center; }
        .footer p { margin: 0; font-size: 14px; }
        .footer a { color: #10b981; text-decoration: none; }
        .button { display: inline-block; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-weight: 600; margin: 10px 0; }
        .button:hover { background: linear-gradient(135deg, #047857 0%, #065f46 100%); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Daya!</h1>
            <p>Your Digital Ambassador Journey Begins Here</p>
        </div>

        <div class="content">
            <div class="welcome-message">
                Congratulations, {{ $user->name }}! 🎉
            </div>

            <p>You've successfully joined the Daya Digital Ambassador program. We're excited to have you as part of our community of influencers driving positive change across Africa!</p>

            <div class="account-details">
                <h3>🚀 Your Account Details</h3>
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
                    <span class="detail-label">Referral Code:</span>
                    <span class="detail-value">{{ $referralCode }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Temporary Password:</span>
                    <span class="detail-value">{{ $password }}</span>
                </div>
            </div>

            <div class="password-warning">
                <strong>🔐 Security Notice:</strong> This is a temporary password. Please log in and change it immediately for your account security.
            </div>

            <div class="earning-info">
                <h3>💰 Your Earning Potential</h3>
                <p>As a Digital Ambassador, you can earn through multiple streams:</p>

                <div class="earning-grid">
                    <div class="earning-item">
                        <h4>5% Commission</h4>
                        <p>Earn 5% from every Content Distributor you recruit</p>
                    </div>
                    <div class="earning-item">
                        <h4>Venture Shares</h4>
                        <p>Build ownership in the platform as your network grows</p>
                    </div>
                    <div class="earning-item">
                        <h4>Residual Income</h4>
                        <p>Ongoing earnings from your recruits' scan activities</p>
                    </div>
                </div>
            </div>

            <div class="referral-section">
                <h3>🔗 Your Referral Code</h3>
                <p>Share this code with potential Content Distributors to start earning:</p>
                <div class="referral-code">
                    <strong>{{ $referralCode }}</strong>
                </div>
                <p>Anyone who uses your referral code during registration will be linked to you, and you'll earn commissions from their activities.</p>
            </div>

            <div class="next-steps">
                <h3>🎯 Next Steps</h3>
                <ul class="steps-list">
                    <li>Log in to your account using the credentials above</li>
                    <li>Change your password for security</li>
                    <li>Complete your profile with additional details</li>
                    <li>Start sharing your referral code on your social platforms</li>
                    <li>Connect with other Digital Ambassadors in our community</li>
                    <li>Track your earnings and network growth</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="https://daya.africa/login" class="button">Login to Your Account</a>
            </div>

            <div class="support-section">
                <h3>Need Help?</h3>
                <p>Our support team is here to help you succeed. Don't hesitate to reach out!</p>
                <p>
                    📧 <a href="mailto:support@daya.africa">support@daya.africa</a><br>
                    🌐 <a href="https://daya.africa">daya.africa</a>
                </p>
            </div>
        </div>

        <div class="footer">
            <p><strong>Daya - Digital Distribution Platform</strong></p>
            <p>Empowering communities through innovative distribution solutions</p>
            <p>This email was sent to {{ $user->email }}</p>
        </div>
    </div>
</body>
</html>