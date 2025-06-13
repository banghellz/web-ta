<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #007bff;
            margin: 0;
        }

        .content {
            margin-bottom: 30px;
        }

        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .user-info h3 {
            margin-top: 0;
            color: #495057;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: bold;
            color: #6c757d;
        }

        .value {
            color: #495057;
        }

        .footer {
            text-align: center;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            color: #6c757d;
            font-size: 14px;
        }

        .footer .brand-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .footer .slogan {
            color: #999;
            font-style: italic;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .success-icon {
            color: #28a745;
            font-size: 48px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Cabinex!</h1>
            <p>Your account has been successfully registered</p>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $user->name }}</strong>,</p>

            <p>Congratulations! Your account registration has been completed successfully. You can now access all
                features available in our system.</p>

            <div class="user-info">
                <h3>Your Account Information:</h3>
                <div class="info-row">
                    <span class="label">Name:</span>
                    <span class="value">{{ $userDetail->nama }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value">{{ $user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="label">NIM:</span>
                    <span class="value">{{ $userDetail->nim }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Program Studi:</span>
                    <span class="value">{{ $userDetail->prodi }}</span>
                </div>
                <div class="info-row">
                    <span class="label">No. Koin:</span>
                    <span class="value">{{ $userDetail->no_koin }}</span>
                </div>
                @if ($userDetail->rfid_uid)
                    <div class="info-row">
                        <span class="label">RFID UID:</span>
                        <span class="value">{{ $userDetail->rfid_uid }}</span>
                    </div>
                @endif
            </div>

            <p>You can now log in to your account and start using our services. If you have any questions or need
                assistance, please don't hesitate to contact our support team.</p>

            <p>Thank you for joining us!</p>
        </div>

        <div class="footer">
            <div class="brand-name">Hexamind.co</div>
            <div class="slogan">Thinking in Six Diffrent Ways</div>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>
