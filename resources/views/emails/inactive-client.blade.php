<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>We Miss You!</title>
</head>
<body>
    <h1>Hello, {{ $client->name }}</h1>
    <p>We noticed you haven't logged in for a while.</p>
    <p>We miss having you around!</p>
    <p>
        <a href="{{ config('app.url') . '/login' }}" style="
            display: inline-block;
            padding: 10px 20px;
            background-color: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 5px;">
            Login Now
        </a>
    </p>
    <p>Thank you for being our valued client!</p>
</body>
</html>
