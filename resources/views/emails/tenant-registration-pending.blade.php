<!DOCTYPE html>
<html>
<head>
    <title>Registration Pending</title>
    <style>
        :root {
            --navy-blue: #003366;
            --gold: #FFD700;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .header {
            background-color: var(--navy-blue);
            color: var(--gold);
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            border-bottom: 3px solid var(--gold);
        }
        .content {
            background-color: white;
            padding: 20px;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .credentials {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border: 2px solid var(--navy-blue);
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: var(--navy-blue);
            color: var(--gold);
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
            border: 2px solid var(--gold);
        }
        .button:hover {
            background-color: var(--gold);
            color: var(--navy-blue);
            border-color: var(--navy-blue);
        }
        h3 {
            color: var(--navy-blue);
            border-bottom: 2px solid var(--gold);
            padding-bottom: 5px;
        }
        strong {
            color: var(--navy-blue);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Registration Pending Approval</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $tenant->tenant_name }},</p>
        
        <p>Thank you for registering your department with BukSkwela. Your registration is currently pending approval from our administrators.</p>
        
        <div class="credentials">
            <h3>Your Department Details:</h3>
            <p><strong>Department Name:</strong> {{ $tenant->tenant_name }}</p>
            <p><strong>Subdomain:</strong> {{ $tenant->id }}.{{ env('CENTRAL_DOMAIN') }}</p>
            <p><strong>Admin Email:</strong> {{ $tenant->tenant_email }}</p>
            <p><strong>Temporary Password:</strong> {{ $password }}</p>
        </div>
        
        <p><strong>Important Notes:</strong></p>
        <ul>
            <li>Your registration is being reviewed by our administrators</li>
            <li>This process typically takes 24-48 hours</li>
            <li>You will receive another email once your registration is approved</li>
            <li>Please save your login credentials securely</li>
        </ul>
        
        <p>You can check your registration status by visiting:</p>
        <a href="{{ $loginUrl }}" class="button">Check Status</a>
        
        <p style="margin-top: 30px;">If you have any questions or concerns, please don't hesitate to contact our support team at support@bukskwela.com</p>
        
        <p>Best regards,<br>The BukSkwela Team</p>
    </div>
</body>
</html> 