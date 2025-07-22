# Mailgun Kurulum Rehberi

## 1. Mailgun Hesabı Oluşturma

1. https://www.mailgun.com adresine gidin
2. "Sign Up Free" ile hesap oluşturun
3. Kredi kartı gerekmez (sandbox mode)
4. İlk 3 ay için 5000 ücretsiz e-posta

## 2. Domain Kurulumu

1. Mailgun Dashboard'a girin
2. Sending > Domains
3. "Add New Domain" 
4. Domain'inizi ekleyin (mg.domain.com önerilir)
5. DNS kayıtlarını hosting sağlayıcınıza ekleyin:
   - TXT kayıtları (SPF, DKIM)
   - MX kayıtları
   - CNAME kayıtları

## 3. API Credentials

1. Settings > API Keys
2. Private API key'i kopyalayın
3. Domain'inizi not alın

## 4. Laravel Mailgun Driver Kurulumu

```bash
composer require symfony/mailgun-mailer symfony/http-client
```

## 5. Laravel Konfigürasyonu

.env dosyanıza ekleyin:
```
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.domain.com
MAILGUN_SECRET=your_private_api_key_here
MAIL_FROM_ADDRESS=noreply@domain.com
MAIL_FROM_NAME="Pazaryeri"
```

config/services.php dosyasına ekleyin:
```php
'mailgun' => [
    'domain' => env('MAILGUN_DOMAIN'),
    'secret' => env('MAILGUN_SECRET'),
    'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    'scheme' => 'https',
],
```

## 6. Sandbox Mode (Test için)

Eğer domain doğrulaması yapamıyorsanız:
```
MAILGUN_DOMAIN=sandbox-xxx.mailgun.org
```
(Sadece authorized recipients'a gönderir)

## 7. Test

```bash
php artisan tinker
Mail::raw('Test mesajı', function($m) { $m->to('test@example.com')->subject('Test'); });
```
