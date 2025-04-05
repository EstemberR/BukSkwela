<!DOCTYPE html>
<html>
<head>
    <title>Welcome to {{ $schoolName }}</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4;">
    <!-- Header with BUKSU Colors -->
    <div style="background-color: #003366; padding: 20px 0; text-align: center;">
        <div style="max-width: 600px; margin: 0 auto;">
            <h1 style="color: #FFD700; margin: 0; font-size: 28px; text-transform: uppercase;">{{ $schoolName }}</h1>
            <p style="color: #fff; margin: 5px 0 0 0; font-size: 16px;">School Management System</p>
        </div>
    </div>

    <!-- Main Content -->
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <!-- Welcome Section -->
        <div style="background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; overflow: hidden;">
            <div style="background-color: #003366; color: #fff; padding: 15px 20px;">
                <h2 style="margin: 0; font-size: 20px;">Welcome to Our Academic Community</h2>
            </div>
            <div style="padding: 20px;">
                <p style="margin-top: 0;">Hello <strong>{{ $student->name }}</strong>,</p>
                <p>Your account has been successfully created in our school management system. Below are your account details:</p>
            </div>
        </div>

        <!-- Account Details Section -->
        <div style="background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; padding: 20px;">
            <h3 style="color: #003366; margin-top: 0; border-bottom: 2px solid #FFD700; padding-bottom: 10px;">Account Information</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; width: 120px;"><strong>Name:</strong></td>
                    <td style="padding: 8px 0;">{{ $student->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Email:</strong></td>
                    <td style="padding: 8px 0;">{{ $student->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Student ID:</strong></td>
                    <td style="padding: 8px 0;">{{ $student->student_id }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Password:</strong></td>
                    <td style="padding: 8px 0;">{{ $password }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Login URL:</strong></td>
                    <td style="padding: 8px 0;"><a href="{{ $loginUrl }}" style="color: #003366; text-decoration: none; border-bottom: 1px solid #003366;">{{ $loginUrl }}</a></td>
                </tr>
            </table>
        </div>

        <!-- Security Notice -->
        <div style="background-color: #FFF3CD; border-left: 4px solid #FFD700; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
            <p style="margin: 0; color: #856404;"><strong>Important Security Notice:</strong> Please change your password after your first login for security purposes.</p>
        </div>

        <!-- Login Instructions -->
        <div style="background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; padding: 20px;">
            <h3 style="color: #003366; margin-top: 0; border-bottom: 2px solid #FFD700; padding-bottom: 10px;">How to Access Your Account</h3>
            <ol style="margin: 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;">Visit <a href="{{ $loginUrl }}" style="color: #003366; text-decoration: none; border-bottom: 1px solid #003366;">{{ $loginUrl }}</a></li>
                <li style="margin-bottom: 8px;">Enter your email address: <strong>{{ $student->email }}</strong></li>
                <li style="margin-bottom: 8px;">Enter your password (provided above)</li>
                <li style="margin-bottom: 8px;">Click "Login"</li>
            </ol>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 30px; padding: 20px; border-top: 1px solid #ddd;">
            <p style="margin: 0 0 10px 0;"><strong>Best regards,</strong><br>{{ $schoolName }} Administration</p>
            <div style="font-size: 12px; color: #666; margin-top: 20px;">
                <p style="margin: 0 0 5px 0;">This is an automated message, please do not reply to this email.</p>
                <p style="margin: 0;">For security reasons, please delete this email after changing your password.</p>
            </div>
        </div>
    </div>

    <!-- BUKSU Footer Banner -->
    <div style="background-color: #003366; color: #fff; text-align: center; padding: 15px 0; font-size: 12px;">
        <p style="margin: 0;">&copy; {{ date('Y') }} {{ $schoolName }}. All rights reserved.</p>
    </div>
</body>
</html> 