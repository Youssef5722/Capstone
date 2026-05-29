<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password — CMS Student Portal</title>
    <style>
        body { margin: 0; padding: 0; background: #0f0f13; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #1a1a24; border-radius: 16px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); }
        .header { background: linear-gradient(135deg, #10b981 0%, #0affff 100%); padding: 36px 40px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; color: #fff; letter-spacing: 0.5px; }
        .header p { margin: 6px 0 0; font-size: 13px; color: rgba(255,255,255,0.8); }
        .body { padding: 40px; color: #c8c8d8; font-size: 15px; line-height: 1.7; }
        .body p { margin: 0 0 20px; }
        .btn-wrap { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #10b981, #0affff); color: #fff; text-decoration: none; padding: 14px 36px; border-radius: 50px; font-size: 15px; font-weight: 600; letter-spacing: 0.3px; }
        .link-fallback { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 14px 18px; word-break: break-all; font-size: 12px; color: #888; margin-top: 8px; }
        .footer { padding: 24px 40px; border-top: 1px solid rgba(255,255,255,0.07); text-align: center; font-size: 12px; color: #555; }
        .expiry { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #f87171; margin-top: 4px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🎓 Password Reset — Student Portal</h1>
        <p>Capstone Management System — Beni-Suef Technological University</p>
    </div>
    <div class="body">
        <p>We received a request to reset the password for your student account associated with <strong>{{ $email }}</strong>.</p>
        <p>Click the button below to choose a new password. This link will expire in <strong>60 minutes</strong>.</p>

        <div class="btn-wrap">
            <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>
        </div>

        <div class="expiry">
            ⏳ This link expires in 60 minutes. After that, you'll need to request a new reset link.
        </div>

        <p style="margin-top:24px;">If you didn't request a password reset, you can safely ignore this email. Your password will not change unless you follow the link above.</p>

        <p style="font-size:13px;color:#666;margin-top:8px;">If the button above doesn't work, copy and paste the link below into your browser:</p>
        <div class="link-fallback">{{ $resetUrl }}</div>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} CMS — Capstone Management System &nbsp;|&nbsp; Beni-Suef Technological University
    </div>
</div>
</body>
</html>
