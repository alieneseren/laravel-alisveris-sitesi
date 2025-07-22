# LARAVEL PAZARYERI HOSTING MIGRATION REHBERİ

## ⚠️ ÖNEMLİ NOT
Bu bir **Laravel PHP Framework** projesidir, WordPress değildir!

## 📋 Migration Gereksinimleri

### 1. Sunucu Gereksinimleri
- PHP 8.1+ (8.2 önerilir)
- MySQL 5.7+ veya 8.0+
- Composer
- Apache/Nginx
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- BCMath PHP Extension

### 2. Upload Edilecek Dosyalar

#### A. Ana Proje Dosyaları
```
/public_html/ (veya web root)
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/ (777 permission)
├── public/ (web root içeriği)
├── composer.json
├── artisan
└── .env (production ayarları)
```

#### B. Database Dump
- `pazaryeri2_backup.sql` - MySQL veritabanı

#### C. Upload Dosyaları
- `public/uploads/` klasörü ve içindeki tüm görseller

### 3. Hosting Kurulum Adımları

#### Adım 1: Dosya Upload
1. Tüm proje dosyalarını hosting'e upload edin
2. `public/` klasörünün içeriğini web root'a taşıyın
3. `.env.production` dosyasını `.env` olarak yeniden adlandırın

#### Adım 2: Database Import
1. Hosting control panel'dan MySQL database oluşturun
2. `pazaryeri2_backup.sql` dosyasını import edin
3. `.env` dosyasında database bilgilerini güncelleyin

#### Adım 3: Permissions
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/uploads/
```

#### Adım 4: Composer Install
```bash
composer install --optimize-autoloader --no-dev
```

#### Adım 5: Laravel Optimize
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

#### Adım 6: Storage Link
```bash
php artisan storage:link
```

### 4. .env Production Ayarları

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=localhost
DB_DATABASE=your_hosting_db_name
DB_USERNAME=your_hosting_db_user
DB_PASSWORD=your_hosting_db_password

SESSION_DOMAIN=yourdomain.com
SESSION_SECURE_COOKIE=true

MAIL_HOST=mail.yourdomain.com
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

### 5. Güvenlik
- `.htaccess_production` dosyasını `.htaccess` olarak kullanın
- SSL sertifikası aktifleştirin
- Security headers aktifleştirin

### 6. Test Edilecek Sayfalar
- Ana sayfa: https://yourdomain.com
- Giriş: https://yourdomain.com/login
- Kayıt: https://yourdomain.com/register
- Ürünler: https://yourdomain.com/urunler
- Admin: https://yourdomain.com/admin/register

### 7. Sorun Giderme
Eğer 500 error alırsanız:
1. storage/ klasörü permissions
2. .env dosyası doğru mu
3. composer install çalıştırıldı mı
4. Laravel cache temizleme

## 📞 Destek
Bu Laravel projesi için özel kurulum gerekiyor.
WordPress migration tools kullanılmamalı!

## 📄 Proje Detayları
- Framework: Laravel 12.20.0
- Database: MySQL
- Payment: PayThor Integration
- Features: Multi-vendor E-commerce Platform
