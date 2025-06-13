{{-- resources/views/emails/account-deletion.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deletion Notification - Cabinex</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }

        .email-header .subtitle {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }

        .email-body {
            padding: 40px 30px;
        }

        .alert-banner {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 500;
        }

        .user-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 6px 6px 0;
        }

        .user-info h3 {
            margin: 0 0 15px 0;
            color: #667eea;
            font-size: 18px;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
            align-items: center;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
            margin-right: 15px;
        }

        .info-value {
            color: #212529;
        }

        .reason-section {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
        }

        .reason-section h4 {
            margin: 0 0 10px 0;
            color: #c53030;
            font-size: 16px;
        }

        .deletion-impacts {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
        }

        .deletion-impacts h4 {
            margin: 0 0 15px 0;
            color: #2d3748;
            font-size: 16px;
        }

        .deletion-impacts ul {
            margin: 0;
            padding-left: 20px;
        }

        .deletion-impacts li {
            margin-bottom: 8px;
            color: #4a5568;
        }

        .contact-section {
            background-color: #e6f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }

        .contact-section h4 {
            margin: 0 0 10px 0;
            color: #2b6cb0;
        }

        .email-footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .footer-text {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
        }

        .footer-brand {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }

        .timestamp {
            background-color: #e9ecef;
            color: #495057;
            padding: 10px 15px;
            border-radius: 4px;
            font-size: 14px;
            display: inline-block;
            margin-top: 15px;
        }

        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .email-header,
            .email-body,
            .email-footer {
                padding: 20px;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-label {
                margin-bottom: 5px;
                min-width: auto;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>Account Deleted</h1>
            <p class="subtitle">Your Cabinex account has been removed from our system</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="alert-banner">
                ⚠️ This is a confirmation that your user account has been permanently deleted
            </div>

            <p>Dear {{ $user->name }},</p>

            <p>We are writing to inform you that your user account in the Cabinex system has been permanently deleted by
                a system administrator.</p>

            <!-- User Information -->
            <div class="user-info">
                <h3>Deleted Account Information</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $user->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Role:</span>
                    <span class="info-value">{{ ucfirst($user->role) }}</span>
                </div>
                @if ($userDetail && $userDetail->nim)
                    <div class="info-row">
                        <span class="info-label">NIM:</span>
                        <span class="info-value">{{ $userDetail->nim }}</span>
                    </div>
                @endif
                @if ($userDetail && $userDetail->prodi)
                    <div class="info-row">
                        <span class="info-label">Program:</span>
                        <span class="info-value">{{ $userDetail->prodi }}</span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Deleted On:</span>
                    <span class="info-value">{{ $deletedAt }}</span>
                </div>
            </div>

            <!-- Deletion Reason (if provided) -->
            @if ($deletionReason)
                <div class="reason-section">
                    <h4>Reason for Deletion</h4>
                    <p style="margin: 0; color: #4a5568;">{{ $deletionReason }}</p>
                </div>
            @endif

            <!-- What This Means -->
            <div class="deletion-impacts">
                <h4>What This Means:</h4>
                <ul>
                    <li><strong>Account Access:</strong> You can no longer log in to the Cabinex system</li>
                    <li><strong>Data Removal:</strong> All your personal data and account information has been
                        permanently deleted</li>
                    @if ($userDetail && $userDetail->rfid_uid)
                        <li><strong>RFID Tag:</strong> Any assigned RFID tags have been released and are available for
                            reassignment</li>
                    @endif
                    <li><strong>Profile Information:</strong> Your profile, including uploaded pictures, has been
                        removed</li>
                    <li><strong>System Records:</strong> You will no longer appear in user listings or reports</li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="contact-section">
                <h4>Need Help or Have Questions?</h4>
                <p style="margin: 0; color: #2b6cb0;">
                    If you believe this deletion was made in error or if you have any questions,
                    please contact the system administrator immediately.
                </p>
            </div>

            <p>Thank you for your time using the Cabinex system.</p>

            <div class="timestamp">
                Notification sent on {{ $deletedAt }}
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p class="footer-text">
                This is an automated message from <a href="#" class="footer-brand">Cabinex</a><br>
                Please do not reply to this email as this mailbox is not monitored.
            </p>
        </div>
    </div>
</body>

</html>
