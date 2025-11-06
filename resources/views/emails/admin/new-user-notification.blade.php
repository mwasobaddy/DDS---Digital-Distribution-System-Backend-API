<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New {{ $userType }} Registration - DDS System</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .user-details { background-color: white; padding: 15px; margin: 15px 0; border-left: 4px solid #007bff; }
        .actions { text-align: center; margin: 20px 0; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
        .footer { text-align: center; font-size: 12px; color: #666; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New {{ $userType }} Registration</h1>
            <p>DDS System Notification</p>
        </div>

        <div class="content">
            <p>Hello {{ $admin->name }},</p>

            <p>A new {{ $userType }} has registered in the DDS system. Here are the details:</p>

            <div class="user-details">
                <h3>{{ $userType }} Information</h3>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Phone:</strong> {{ $user->phone }}</p>
                <p><strong>Country:</strong> {{ $userModel->country ?? 'N/A' }}</p>
                @if($userType === 'DA')
                    <p><strong>Referral Code:</strong> {{ $userModel->referral_code }}</p>
                    <p><strong>Referred By:</strong> {{ $userModel->referred_by_da_id ? 'Another DA' : 'Default Admin' }}</p>
                @elseif($userType === 'DCD')
                    <p><strong>DCD ID:</strong> {{ $userModel->id }}</p>
                    <p><strong>Referring DA:</strong> {{ $userModel->referring_da_id ? 'DA #' . $userModel->referring_da_id : 'None' }}</p>
                @elseif($userType === 'Client')
                    <p><strong>Company:</strong> {{ $userModel->company_name }}</p>
                @endif
                <p><strong>Registration Date:</strong> {{ $user->created_at->format('Y-m-d H:i:s') }}</p>
            </div>

            <div class="actions">
                <p><strong>Admin Actions:</strong></p>
                @if($userType === 'Client')
                    <p>Check for pending campaigns from this client.</p>
                @else
                    <p>Monitor user activity and ensure proper onboarding.</p>
                @endif
            </div>

            <p>This is an automated notification from the DDS system.</p>
        </div>

        <div class="footer">
            <p>DDS (Digital Distribution Service) - Admin System</p>
            <p>This email was sent to {{ $admin->email }}</p>
        </div>
    </div>
</body>
</html>