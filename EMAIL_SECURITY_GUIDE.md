# 🔐 PAZARYERI E-POSTA GÜVENLİK REHBERİ

## GÜVENLİ E-POSTA KURULUMU

### 1. Hosting SMTP Kullanın (ÖNERİLEN):
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=hosting_panel_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Pazaryeri"
```

### 2. Alternatif Güvenli Servisler:

#### SendGrid (Profesyonel):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
```

#### Mailgun:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_smtp_login
MAIL_PASSWORD=your_mailgun_smtp_password
MAIL_ENCRYPTION=tls
```

#### Amazon SES:
```env
MAIL_MAILER=smtp
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=your_aws_access_key
MAIL_PASSWORD=your_aws_secret_key
MAIL_ENCRYPTION=tls
```

## GÜVENLİK KURALLARI:

### ❌ YAPMAYIN:
- Google uygulama şifresi kullanmayın
- Kişisel Gmail hesabı kullanmayın
- Şifreleri kod içinde yazmayın
- HTTP bağlantı kullanmayın

### ✅ YAPIN:
- Domain'inize ait e-posta kullanın
- HTTPS/TLS şifreleme kullanın
- Güçlü şifreler oluşturun
- API key'leri environment'da saklayın
- Düzenli şifre değiştirin

## PAZARYERI İÇİN ÖNERİ:

1. **Domain E-postası**: 
   - noreply@pazaryeri.com
   - info@pazaryeri.com
   - destek@pazaryeri.com

2. **E-posta Türleri**:
   - Kayıt doğrulama
   - Şifre sıfırlama  
   - Sipariş bildirimleri
   - Pazarlama e-postaları

3. **Güvenlik Önlemleri**:
   - Rate limiting (spam koruması)
   - E-posta doğrulama
   - DKIM/SPF kayıtları
   - Unsubscribe linkleri

## GOOGLE UYGULAMA ŞİFRESİ RİSKLERİ:

### Yüksek Risk:
- Tüm Gmail hesabına erişim
- Kişisel e-postaları okuma
- Spam/phishing gönderme
- Hesap devralma saldırıları

### Düşük Risk Alternatifleri:
- Hosting SMTP (sınırlı erişim)
- Transactional email servisleri
- API tabanlı e-posta servisleri

## ACİL DURUM PLANI:

Şifre ele geçirilirse:
1. Hemen Google hesabından uygulama şifresini iptal edin
2. Aktif oturumları sonlandırın
3. Güvenlik loglarını kontrol edin
4. Alternatif SMTP'ye geçin
5. Tüm şifreleri değiştirin
