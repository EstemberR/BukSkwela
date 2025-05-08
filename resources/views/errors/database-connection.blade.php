<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 40px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .error-icon {
            color: #dc3545;
            font-size: 48px;
            margin-bottom: 20px;
        }
        .error-title {
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .error-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-family: monospace;
        }
        .solution-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #e8f4ff;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="text-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#dc3545" class="bi bi-database-x" viewBox="0 0 16 16">
                    <path d="M12.096 6.223A4.92 4.92 0 0 0 13 5.698V7c0 .289-.213.654-.753 1.007a4.493 4.493 0 0 1 1.753.25V4c0-1.007-.875-1.755-1.904-2.223C11.022 1.289 9.573 1 8 1s-3.022.289-4.096.777C2.875 2.245 2 2.993 2 4v9c0 1.007.875 1.755 1.904 2.223C4.978 15.71 6.427 16 8 16c.536 0 1.058-.034 1.555-.097a4.525 4.525 0 0 1-.813-.927C8.5 14.992 8.252 15 8 15c-1.464 0-2.766-.27-3.682-.687C3.356 13.875 3 13.373 3 13v-1.302c.271.202.58.378.904.525C4.978 12.71 6.427 13 8 13h.027a4.552 4.552 0 0 1 0-1H8c-1.464 0-2.766-.27-3.682-.687C3.356 10.875 3 10.373 3 10V8.698c.271.202.58.378.904.525C4.978 9.71 6.427 10 8 10c.262 0 .52-.008.774-.024a4.525 4.525 0 0 1 1.102-1.132C9.298 8.944 8.666 9 8 9c-1.464 0-2.766-.27-3.682-.687C3.356 7.875 3 7.373 3 7V5.698c.271.202.58.378.904.525C4.978 6.711 6.427 7 8 7s3.022-.289 4.096-.777ZM3 4c0-.374.356-.875 1.318-1.313C5.234 2.271 6.536 2 8 2s2.766.27 3.682.687C12.644 3.125 13 3.627 13 4c0 .374-.356.875-1.318 1.313C10.766 5.729 9.464 6 8 6s-2.766-.27-3.682-.687C3.356 4.875 3 4.373 3 4Z"/>
                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm-.646-4.854.646.647.646-.647a.5.5 0 0 1 .708.708l-.647.646.647.646a.5.5 0 0 1-.708.708l-.646-.647-.646.647a.5.5 0 0 1-.708-.708l.647-.646-.647-.646a.5.5 0 0 1 .708-.708Z"/>
                </svg>
            </div>
            <h1 class="error-title text-center">Database Connection Error</h1>
            <p class="text-center mb-4">We're having trouble connecting to your database</p>
            
            <div class="alert alert-danger">
                <p><strong>There was an error connecting to your tenant database.</strong> This might be due to the following reasons:</p>
                <ul>
                    <li>The database hasn't been created yet</li>
                    <li>There's a configuration issue with the database connection</li>
                    <li>The database server might be temporarily unavailable</li>
                </ul>
            </div>
            
            <div class="error-details">
                <p><strong>Technical details:</strong> {{ $error }}</p>
                <p><strong>Tenant ID:</strong> {{ $tenant }}</p>
                <p><strong>Database:</strong> {{ $database }}</p>
            </div>
            
            <div class="solution-section">
                <h4>How to fix this issue:</h4>
                <ol>
                    <li>Make sure your database server is running</li>
                    <li>Check that the database named '{{ $database }}' exists</li>
                    <li>Verify that your database credentials in .env file are correct</li>
                    <li>Run the tenant database setup command: <code>php artisan tenant:fix-database {{ $tenant }}</code></li>
                </ol>
                
                <p class="mt-3">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Go Back</a>
                    <a href="{{ route('tenant.dashboard', ['tenant' => $tenant]) }}" class="btn btn-primary">Go to Dashboard</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html> 