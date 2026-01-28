<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $title }}">
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
            background-color: #fff;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            font-size: 2.5em;
            margin-bottom: 0.5em;
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 0.3em;
        }
        
        h2 {
            font-size: 2em;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
            color: #34495e;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 0.3em;
        }
        
        h3 {
            font-size: 1.5em;
            margin-top: 1.2em;
            margin-bottom: 0.5em;
            color: #34495e;
        }
        
        h4 {
            font-size: 1.2em;
            margin-top: 1em;
            margin-bottom: 0.5em;
            color: #7f8c8d;
        }
        
        p {
            margin-bottom: 1em;
            text-align: justify;
        }
        
        ul, ol {
            margin-left: 2em;
            margin-bottom: 1em;
        }
        
        li {
            margin-bottom: 0.5em;
        }
        
        code {
            background-color: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            color: #e74c3c;
        }
        
        pre {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            margin-bottom: 1em;
        }
        
        pre code {
            background-color: transparent;
            color: #ecf0f1;
            padding: 0;
        }
        
        a {
            color: #3498db;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        strong {
            font-weight: 600;
            color: #2c3e50;
        }
        
        em {
            font-style: italic;
        }
        
        .last-updated {
            margin-top: 3em;
            padding-top: 1em;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 0.9em;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            h1 {
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
        {!! $content !!}
        
        <div class="last-updated">
            <p><strong>Last Updated:</strong> January 2026</p>
        </div>
    </div>
</body>
</html>

