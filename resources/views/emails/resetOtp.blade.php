<!DOCTYPE html>
<html>

<head>
    <title>Otp for reset password</title>
</head>

<body>
    <h2>Dear {{ $user->name }},</h2>

    <p>Your one-time password (OTP) for reset password is: {{ $user->otp }} </p>
    {{-- <p>Thank you for signing up! </p> --}}

    <p>Regards,</p>
    <p>Foundit Team</p>
</body>

</html>
