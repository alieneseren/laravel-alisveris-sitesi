<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-posta Doğrulama</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .otp-code {
            background-color: #f8f9fa;
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 20px;
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 5px;
            margin: 20px 0;
        }
        .info {
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>E-posta Doğrulama</h1>
        </div>
        
        <div class="content">
            <h2>Merhaba {{ $kullaniciAdi }}!</h2>
            <p>Hesabınızı doğrulamak için aşağıdaki 6 haneli kodu kullanın:</p>
            
            <div class="otp-code">
                {{ $otp }}
            </div>
            
            <div class="info">
                <p><strong>Bu kod 10 dakika süreyle geçerlidir.</strong></p>
                <p>Eğer bu işlemi siz yapmadıysanız, bu e-postayı dikkate almayın.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Bu e-posta otomatik olarak gönderilmiştir, lütfen yanıtlamayın.</p>
            <p>&copy; {{ date('Y') }} Pazaryeri. Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>
