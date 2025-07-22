# SendGrid Kurulum Rehberi

## 1. SendGrid Hesabı Oluşturma

1. https://sendgrid.com adresine gidin
2. "Start for Free" ile ücretsiz hesap oluşturun
3. E-posta doğrulamasını tamamlayın
4. Günlük 100 e-posta limiti ile başlayabilirsiniz

## 2. API Key Oluşturma

1. SendGrid Dashboard'a girin
2. Settings > API Keys'e gidin
3. "Create API Key" butonuna tıklayın
4. "Restricted Access" seçin
5. Mail Send için "Full Access" verin
6. API Key'i kopyalayın (bir daha gösterilmez!)

## 3. Domain Doğrulama (Önerilen)

1. Settings > Sender Authentication
2. "Authenticate Your Domain" 
3. Domain'inizi ekleyin (örn: pazaryeri.com)
4. DNS kayıtlarını hosting sağlayıcınıza ekleyin

## 4. Sender Identity Oluşturma

1. Settings > Sender Authentication
2. "Create a Single Sender"
3. E-posta: noreply@domain.com
4. From Name: Pazaryeri
5. Reply To: support@domain.com

## 5. Laravel Entegrasyonu

.env dosyanıza ekleyin:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@domain.com
MAIL_FROM_NAME="Pazaryeri"
```

## 6. Test

```bash
php artisan tinker
Mail::raw('Test mesajı', function($m) { $m->to('test@example.com')->subject('Test'); });
```
