<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Daya - Your Account Details</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .credentials { background-color: #e8f4f8; padding: 15px; margin: 15px 0; border-left: 4px solid #007bff; }
        .warning { background-color: #fff3cd; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107; color: #856404; }
        .footer { text-align: center; font-size: 12px; color: #666; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Daya!</h1>
            <p>Your Digital Distribution Platform</p>
        </div>

        <div class="content">
            <p>Hello {{ $user->name }},</p>

            <p>Welcome to Daya! Your account has been created successfully and you're ready to start your digital distribution journey.</p>

            <div class="credentials">
                <h3>Your Account Details</h3>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
            </div>

            <div class="warning">
                <p><strong>Important Security Notice:</strong> Please change your password after your first login for security purposes.</p>
            </div>

            <p>You can now log in to your account and track your campaigns, view analytics, and manage your digital distribution efforts.</p>

            <p>If you have any questions or need assistance, our support team is here to help.</p>

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