<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
</head>
<body>
    <h1>Hello, {{ $client->name }}</h1>
    <p>We received a request to reset your password.</p>
    <p>If you made this request, you can reset your password by clicking the button below:</p>
    <p>
        <a href="{{ config('app.url') . '/reset-password/' . $client->reset_token . '?email=' . $client->email }}" style="
            display: inline-block;
            padding: 10px 20px;
            background-color: #38c172;
            color: white;
            text-decoration: none;
            border-radius: 5px;">
            Reset Password
        </a>
    </p>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Thank you!</p>
</body>
</html>
