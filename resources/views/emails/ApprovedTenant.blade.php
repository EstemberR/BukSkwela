<!DOCTYPE html>
<html>
<head>
    <title>Your Tenant Account Has Been Approved</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4;">
    <!-- Header with BUKSU Colors -->
    <div style="background-color: #003366; padding: 20px 0; text-align: center;">
        <div style="max-width: 600px; margin: 0 auto;">
            <h1 style="color: #FFD700; margin: 0; font-size: 28px; text-transform: uppercase;">BukSkwela</h1>
            <p style="color: #fff; margin: 5px 0 0 0; font-size: 16px;">School Management System</p>
        </div>
    </div>

    <!-- Main Content -->
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <!-- Welcome Section -->
        <div style="background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; overflow: hidden;">
            <div style="background-color: #003366; color: #fff; padding: 15px 20px;">
                <h2 style="margin: 0; font-size: 20px;">Congratulations! Your Tenant Account Has Been Approved</h2>
            </div>
            <div style="padding: 20px;">
                <p style="margin-top: 0;">Hello <strong>{{ $tenant->data['admin_name'] ?? 'Administrator' }}</strong>,</p>
                <p>We are pleased to inform you that your tenant account <strong>{{ $tenant->tenant_name }}</strong> has been approved. You can now access all the features of our school management system.</p>
            </div>
        </div>

        <!-- Account Details Section -->
        <div style="background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; padding: 20px;">
            <h3 style="color: #003366; margin-top: 0; border-bottom: 2px solid #FFD700; padding-bottom: 10px;">Tenant Information</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; width: 150px;"><strong>Tenant Name:</strong></td>
                    <td style="padding: 8px 0;">{{ $tenant->tenant_name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Admin Email:</strong></td>
                    <td style="padding: 8px 0;">{{ $tenant->tenant_email }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Status:</strong></td>
                    <td style="padding: 8px 0;"><span style="color: #28a745; font-weight: bold;">{{ ucfirst($tenant->status) }}</span></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Subscription Plan:</strong></td>
                    <td style="padding: 8px 0;">{{ ucfirst($tenant->subscription_plan) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Login URL:</strong></td>
                    <td style="padding: 8px 0;"><a href="{{ $loginUrl }}" style="color: #003366; text-decoration: none; border-bottom: 1px solid #003366;">{{ $loginUrl }}</a></td>
                </tr>
            </table>
        </div>

        <!-- What's Next Section -->
        <div style="background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; padding: 20px;">
            <h3 style="color: #003366; margin-top: 0; border-bottom: 2px solid #FFD700; padding-bottom: 10px;">What's Next?</h3>
            <ul style="margin: 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;">Log in to your tenant dashboard using the link above</li>
                <li style="margin-bottom: 8px;">Set up your school profile and customize your settings</li>
                <li style="margin-bottom: 8px;">Create staff accounts and start managing your school</li>
                <li style="margin-bottom: 8px;">Explore our features including requirements management, student records, and more</li>
            </ul>
        </div>

        <!-- Support Section -->
        <div style="background-color: #E8F4FD; border-left: 4px solid #0275d8; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
            <p style="margin: 0; color: #0275d8;"><strong>Need Help?</strong> If you have any questions or need assistance, please contact our support team at <a href="mailto:support@bukskwela.com" style="color: #0275d8;">support@bukskwela.com</a>.</p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 30px; padding: 20px; border-top: 1px solid #ddd;">
            <p style="margin: 0 0 10px 0;"><strong>Best regards,</strong><br>BukSkwela Administration</p>
            <div style="font-size: 12px; color: #666; margin-top: 20px;">
                <p style="margin: 0 0 5px 0;">This is an automated message, please do not reply to this email.</p>
            </div>
        </div>
    </div>

    <!-- BUKSU Footer Banner -->
    <div style="background-color: #003366; color: #fff; text-align: center; padding: 15px 0; font-size: 12px;">
        <p style="margin: 0;">&copy; {{ date('Y') }} BukSkwela. All rights reserved.</p>
    </div>
</body>
</html>
