<!DOCTYPE html>
<html>
<head>
    <title>Kode Verifikasi</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .code { 
            font-size: 24px; 
            font-weight: bold; 
            margin: 20px 0; 
            padding: 10px;
            background: #f4f4f4;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Kode Verifikasi Anda</h2>
        <p>Gunakan kode berikut untuk memverifikasi akun Anda:</p>
        
        <div class="code">
            {{ $verificationCode }}
        </div>
        
        <p>Kode ini akan kadaluarsa dalam 24 jam.</p>
        <p>Jika Anda tidak meminta kode ini, Anda bisa mengabaikan email ini.</p>
    </div>
</body>
</html>