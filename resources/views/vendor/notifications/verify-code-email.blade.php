<!DOCTYPE html>
<html>
<head>
    <title>Kode Verifikasi Reset Password</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style type="text/css">
        * { font-family: 'Poppins', Arial, sans-serif; }
        body { margin: 0; padding: 0; background-color: #f4f4f4; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 5px; overflow: hidden; }
        .email-header { background-color: #4e73df; padding: 20px; text-align: center; }
        .email-header h1 { color: white; margin: 0; font-size: 24px; }
        .email-body { padding: 30px; color: #333; }
        .email-body p { margin: 0 0 15px; line-height: 1.5; }
        .code-box { font-size: 32px; font-weight: bold; letter-spacing: 8px; background: #f8f9fc; padding: 16px 0; text-align: center; border-radius: 8px; margin: 20px 0; }
        .email-footer { background-color: #f8f9fc; padding: 15px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Monoframe Studio</h1>
        </div>
        <div class="email-body">
            <p>Halo,</p>
            <p>Berikut adalah kode verifikasi untuk reset password akun Anda:</p>
            <div class="code-box">{{ $code }}</div>
            <p>Kode ini akan kedaluwarsa dalam 10 menit.</p>
            <p>Jika Anda tidak meminta reset password, tidak ada tindakan lebih lanjut yang diperlukan.</p>
            <p>Salam,<br>Tim Monoframe Studio</p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} Monoframe Studio. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
