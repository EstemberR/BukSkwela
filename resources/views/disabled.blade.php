<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Disabled - BukSkwela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color:rgb(240, 243, 247);
            font-family: 'Arial', sans-serif;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .content-container {
            max-width: 700px;
            width: 100%;
            text-align: center;
            padding: 20px 40px;
            margin-top: 30px;
            background-color:rgb(240, 243, 247);
            border-radius: 0;
            box-shadow: none;
        }
        .icon-container {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 20px;
            
        }
        h1 {
            color: #dc3545;
            margin-bottom: 20px;
        }
        .subdomain-name {
            font-weight: bold;
            color: #dc3545;
        }
        p {
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .img-fluid {
            max-width: 100%;
            height: auto;
            margin-bottom: 30px;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 10px 30px;
            margin-right: 10px;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 10px 30px;
        }
        .btn-secondary:hover {
            background-color: #5c636a;
        }
        .btn-group {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="content-container">
     
        
        <h1>Tenant Disabled</h1>
        
        @php
            // Get the current subdomain
            $host = request()->getHost();
            $parts = explode('.', $host);
            $subdomain = null;
            
            // For localhost with port (e.g., testing.localhost:8000)
            if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
                if (count($parts) >= 2) {
                    $subdomain = $parts[0];
                }
            } else if (count($parts) > 2) {
                $subdomain = $parts[0];
            }
        @endphp
        
        @if($subdomain)
            <p>The BukSkwela Tenant for <span class="subdomain-name">{{ strtoupper($subdomain) }}</span> has been disabled by the administrator.</p>
        @else
            <p>This BukSkwela account has been disabled. Please contact your administrator for more information.</p>
        @endif
        <div class="text-center mb-4">
            <img src="https://blog.wrappixel.com/wp-content/uploads/2022/05/woman-working-on-laptop-2.gif" 
                 class="img-fluid" 
                 alt="Woman working on laptop">
        </div>
        
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> If you believe this is an error, please contact the BukSkwela support team for assistance.
        </div>
        
       
        
        <div class="btn-group">
            <a href="mailto:support@bukskwela.com" class="btn btn-primary">
                <i class="fas fa-envelope mr-2"></i> Contact Support
            </a>
        </div>
    </div>
</body>
</html>