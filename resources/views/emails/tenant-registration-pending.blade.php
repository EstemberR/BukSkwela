<!DOCTYPE html>
<html>
<head>
    <title>Registration Pending</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
    <div style="background-color: #003366; color: #FFD700; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; border-bottom: 3px solid #FFD700;">
        <h1 style="margin: 0; font-size: 24px;">Registration Pending Approval</h1>
    </div>
    
    <div style="background-color: white; padding: 20px; border-radius: 0 0 5px 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <p style="font-size: 16px; margin-bottom: 20px;">Dear {{ $tenant->tenant_name }},</p>
        
        <p style="font-size: 16px; margin-bottom: 20px;">Thank you for registering your department with BukSkwela. Your registration is currently pending approval from our administrators.</p>
        
        <div style="background-color: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px; border: 2px solid #003366;">
            <h3 style="color: #003366; border-bottom: 2px solid #FFD700; padding-bottom: 10px; margin-top: 0;">Your Department Details:</h3>
            <p style="margin: 10px 0;"><strong style="color: #003366;">Department Name:</strong> {{ $tenant->tenant_name }}</p>
            <p style="margin: 10px 0;"><strong style="color: #003366;">Subdomain:</strong> {{ $tenant->id }}.{{ env('CENTRAL_DOMAIN') }}</p>
            <p style="margin: 10px 0;"><strong style="color: #003366;">Admin Email:</strong> {{ $tenant->tenant_email }}</p>
            <p style="margin: 10px 0;"><strong style="color: #003366;">Temporary Password:</strong> {{ $password }}</p>
            <p style="margin: 10px 0;"><strong style="color: #003366;">Login URL:</strong> <a href="{{ $loginUrl }}" style="color: #003366; text-decoration: underline;">{{ $loginUrl }}</a></p>
        </div>
        
        <p style="font-size: 16px; margin-top: 25px;"><strong style="color: #003366;">Important Notes:</strong></p>
        <ul style="padding-left: 20px; margin: 15px 0;">
            <li style="margin-bottom: 10px;">Your registration is being reviewed by our administrators</li>
            <li style="margin-bottom: 10px;">This process typically takes 24-48 hours</li>
            <li style="margin-bottom: 10px;">You will receive another email once your registration is approved</li>
            <li style="margin-bottom: 10px;">Please save your login credentials securely</li>
        </ul>
        
        <p style="font-size: 16px; margin-top: 25px;">You can log in to your account using the credentials above by clicking the button below:</p>
        <table cellpadding="0" cellspacing="0" border="0" style="margin: 20px 0;">
            <tr>
                <td>
                    <a href="{{ $loginUrl }}" style="display: inline-block; padding: 12px 24px; background-color: #003366; color: #FFD700; text-decoration: none; border-radius: 5px; font-weight: bold; border: 2px solid #FFD700;">Login to Your Account</a>
                </td>
            </tr>
        </table>
        
        <p style="margin-top: 30px; color: #666666;">If you have any questions or concerns, please don't hesitate to contact our support team at <a href="mailto:support@bukskwela.com" style="color: #003366; text-decoration: underline;">support@bukskwela.com</a></p>
        
        <p style="margin-top: 30px; color: #666666;">
            Best regards,<br>
            <strong style="color: #003366;">The BukSkwela Team</strong>
        </p>
    </div>
</body>
</html> 