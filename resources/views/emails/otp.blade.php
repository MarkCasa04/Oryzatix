<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>ORYZATIX Password Reset Verification Code</title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      background-color: #f4f6f8;
      margin: 0;
      padding: 30px 15px;
      color: #1e293b;
    }
    .email-container {
      max-width: 540px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      border: 1px solid #e2e8f0;
    }
    .email-header {
      background: linear-gradient(135deg, #0d4a18 0%, #15803d 100%);
      padding: 32px 24px;
      text-align: center;
      color: #ffffff;
    }
    .email-header h1 {
      margin: 0;
      font-size: 24px;
      font-weight: 800;
      letter-spacing: -0.5px;
    }
    .email-header p {
      margin: 6px 0 0;
      font-size: 13px;
      opacity: 0.9;
    }
    .email-body {
      padding: 32px 28px;
    }
    .greeting {
      font-size: 16px;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 12px;
    }
    .instruction {
      font-size: 14px;
      line-height: 1.6;
      color: #475569;
      margin-bottom: 24px;
    }
    .otp-card {
      background: #f0fdf4;
      border: 2px dashed #86efac;
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      margin: 20px 0 28px;
    }
    .otp-label {
      font-size: 11.5px;
      font-weight: 800;
      color: #166534;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 8px;
    }
    .otp-code {
      font-size: 34px;
      font-weight: 900;
      letter-spacing: 8px;
      color: #15803d;
      font-family: 'Courier New', Courier, monospace;
      margin: 0;
    }
    .otp-expiry {
      font-size: 12px;
      color: #64748b;
      margin-top: 8px;
    }
    .security-notice {
      background: #fffbeb;
      border-left: 4px solid #f59e0b;
      padding: 12px 16px;
      border-radius: 6px;
      font-size: 12.5px;
      color: #92400e;
      line-height: 1.5;
      margin-bottom: 24px;
    }
    .email-footer {
      background: #f8fafc;
      padding: 20px 24px;
      border-top: 1px solid #e2e8f0;
      text-align: center;
      font-size: 12px;
      color: #94a3b8;
      line-height: 1.5;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="email-header">
      <h1>ORYZATIX</h1>
      <p>Rice Leaf Health Diagnostic & Management System</p>
    </div>

    <div class="email-body">
      <div class="greeting">Hello, {{ $name }}!</div>
      <div class="instruction">
        We received a request to reset the password for your ORYZATIX account associated with this email address (<strong>{{ $email }}</strong>).
      </div>

      <div class="otp-card">
        <div class="otp-label">Verification Code (OTP)</div>
        <div class="otp-code">{{ $otp }}</div>
        <div class="otp-expiry">Valid for 15 minutes</div>
      </div>

      <div class="security-notice">
        <strong>Security Notice:</strong> Never share this verification code with anyone. If you did not request this password reset, please ignore this email and your account will remain secure.
      </div>

      <p style="font-size: 13px; color: #64748b; margin: 0;">
        Best regards,<br>
        <strong>ORYZATIX Agronomic Diagnostics Team</strong>
      </p>
    </div>

    <div class="email-footer">
      This is an automated system notification from ORYZATIX AI Diagnostic System.<br>
      © {{ date('Y') }} ORYZATIX. All rights reserved.
    </div>
  </div>
</body>
</html>
