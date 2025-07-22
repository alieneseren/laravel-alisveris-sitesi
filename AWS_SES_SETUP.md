# Amazon SES Kurulum Rehberi

## 1. AWS Hesabı ve SES Kurulumu

1. https://aws.amazon.com adresine gidin
2. AWS hesabı oluşturun (kredi kartı gerekli ama ücretsiz tier var)
3. SES konsola gidin (us-east-1 region önerilir)
4. Önce "Sandbox mode"da başlayabilirsiniz

## 2. E-posta Adresi Doğrulama

1. SES Console > Verified identities
2. "Create identity" 
3. E-posta adresinizi ekleyin
4. Doğrulama e-postasına tıklayın

## 3. Domain Doğrulama (Production için)

1. "Create identity" > Domain
2. Domain'inizi ekleyin
3. DNS kayıtlarını hosting sağlayıcınıza ekleyin
4. Production access için AWS Support'a başvurun

## 4. IAM User ve Access Keys

1. IAM Console > Users > Create user
2. Username: ses-smtp-user
3. "Attach policies directly" > AmazonSESFullAccess
4. Security credentials > Create access key
5. "Other" seçin
6. Access Key ID ve Secret Access Key'i kaydedin

## 5. SMTP Credentials Oluşturma

1. SES Console > SMTP settings
2. "Create SMTP credentials"
3. IAM User Name: ses-smtp-user
4. SMTP username ve password'u kaydedin

## 6. Laravel Konfigürasyonu

.env dosyanıza ekleyin:
```
MAIL_MAILER=smtp
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=verified@yourdomain.com
MAIL_FROM_NAME="Pazaryeri"

# SES Region
AWS_DEFAULT_REGION=us-east-1
```

## 7. Laravel SES Driver (Alternatif)

```bash
composer require aws/aws-sdk-php
```

.env:
```
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
```

## 8. Production Erişimi

Sandbox mode'dan çıkmak için:
1. SES Console > Account dashboard
2. "Request production access"
3. Use case: "Transactional emails"
4. Günlük gönderim limitinizi belirtin

## 9. Test

```bash
php artisan tinker
Mail::raw('Test mesajı', function($m) { $m->to('test@example.com')->subject('Test'); });
```
