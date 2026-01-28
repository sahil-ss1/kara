<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect HubSpot - Kara</title>
    <meta name="description" content="Connect your HubSpot account to Kara to sync deals, manage teams, and track performance.">
    <meta name="robots" content="index, follow">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            text-align: center;
        }
        
        .logo {
            margin-bottom: 30px;
        }
        
        .logo h1 {
            font-size: 2.5em;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .logo p {
            color: #7f8c8d;
            font-size: 1.1em;
        }
        
        .icon {
            font-size: 4em;
            color: #ff7b31;
            margin-bottom: 20px;
        }
        
        h2 {
            font-size: 1.8em;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .description {
            color: #555;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        
        .benefits {
            text-align: left;
            margin: 30px 0;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        
        .benefits h3 {
            font-size: 1.2em;
            color: #2c3e50;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .benefits ul {
            list-style: none;
            padding: 0;
        }
        
        .benefits li {
            padding: 10px 0;
            padding-left: 30px;
            position: relative;
            color: #555;
        }
        
        .benefits li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #27ae60;
            font-weight: bold;
            font-size: 1.2em;
        }
        
        .connect-button {
            display: inline-block;
            background-color: #ff7b31;
            color: #fff;
            padding: 15px 40px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: 600;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            margin: 20px 0;
        }
        
        .connect-button:hover {
            background-color: #e66a1f;
            color: #fff;
        }
        
        .connect-button i {
            margin-right: 10px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 0.9em;
        }
        
        .info a {
            color: #3498db;
            text-decoration: none;
        }
        
        .info a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }
            
            .logo h1 {
                font-size: 2em;
            }
            
            h2 {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>Kara</h1>
            <p>Sales Team Management Platform</p>
        </div>
        
        <div class="icon">
            <i class="fa-brands fa-hubspot"></i>
        </div>
        
        <h2>Connect Your HubSpot Account</h2>
        
        <p class="description">
            Connect Kara with your HubSpot CRM to sync deals, manage teams, and track performance in one place.
        </p>
        
        @if(session('success'))
            <div class="alert alert-success">
                <strong>Success!</strong> {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">
                <strong>Error:</strong> {{ session('error') }}
            </div>
        @endif
        
        <div class="benefits">
            <h3>What You'll Get</h3>
            <ul>
                <li>Automatic sync of deals, pipelines, and team members</li>
                <li>Track team goals and performance metrics</li>
                <li>Manage 1-on-1 meetings with your sales team</li>
                <li>AI-powered deal briefings for better coaching</li>
                <li>Comprehensive dashboards and analytics</li>
            </ul>
        </div>
        
        <a href="{{ route('hubspot.login') }}" class="connect-button">
            <i class="fa-brands fa-hubspot"></i>
            Connect HubSpot Account
        </a>
        
        <div class="info">
            <p>
                By connecting, you authorize Kara to access your HubSpot data as specified in our 
                <a href="{{ route('docs.terms-of-service') }}" target="_blank">Terms of Service</a>, 
                <a href="{{ route('docs.privacy-policy') }}" target="_blank">Privacy Policy</a>, and 
                <a href="{{ route('docs.security-policy') }}" target="_blank">Security Policy</a>.
            </p>
            <p style="margin-top: 10px;">
                <strong>Documentation:</strong><br>
                <a href="{{ route('docs.hubspot-setup-guide') }}" target="_blank">Setup Guide</a> · 
                <a href="{{ route('docs.shared-data') }}" target="_blank">Shared Data</a> · 
                <a href="{{ route('docs.scope-justification') }}" target="_blank">OAuth Scopes</a>
            </p>
        </div>
    </div>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>

