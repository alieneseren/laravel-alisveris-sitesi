# 🚀 PAZARYERI HOSTING TAŞIMA REHBERİ

## 📋 GEREKSİNİMLER

### Hosting Gereksinimleri:
- PHP 8.1+ (önerilen 8.2)
- MySQL 5.7+ veya 8.0+
- Apache/Nginx web server
- Composer
- SSL sertifikası
- Minimum 512MB RAM (önerilen 1GB+)
- 500MB+ disk alanı

### PHP Extensions:
- PDO
- Mbstring
- OpenSSL
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD (resim işleme için)

## 📂 1. DOSYA YAPILANDIRMASI

### Hosting'e Yüklenecek Dosyalar:
```
public_html/               (hosting'in public klasörü)
├── index.php              (Laravel'in public/index.php)
├── .htaccess              (güncellenmiş .htaccess)
├── css/
├── js/
├── images/
├── uploads/
└── favicon.ico

laravel/                   (public_html dışında güvenli klasör)
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env                   (production ayarları)
├── artisan
├── composer.json
└── composer.lock
```

## 🔧 2. ADIM ADIM KURULUM

### 2.1. Dosyaları Hosting'e Yükleme

1. **FTP/SFTP ile dosya yükleme:**
   - `public/` klasörünün içeriğini → `public_html/` klasörüne
   - Diğer tüm dosyaları → `laravel/` klasörüne (public_html dışında)

2. **Composer bağımlılıklarını yükleme:**
   ```bash
   cd laravel/
   composer install --optimize-autoloader --no-dev
   ```

### 2.2. Environment Ayarları

1. **`.env` dosyasını düzenleme:**
   ```env
   APP_NAME="Pazaryeri"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=hosting_db_name
   DB_USERNAME=hosting_db_user
   DB_PASSWORD=hosting_db_pass
   
   MAIL_MAILER=smtp
   MAIL_HOST=mail.yourdomain.com
   MAIL_PORT=587
   MAIL_USERNAME=noreply@yourdomain.com
   MAIL_PASSWORD=your_mail_password
   ```

2. **index.php yollarını güncelleme:**
   `public_html/index.php` dosyasında:
   ```php
   require __DIR__.'/../laravel/vendor/autoload.php';
   $app = require_once __DIR__.'/../laravel/bootstrap/app.php';
   ```

### 2.3. Veritabanı Kurulumu

1. **Hosting panelinden MySQL veritabanı oluşturma**
2. **Migration'ları çalıştırma:**
   ```bash
   cd laravel/
   php artisan migrate --force
   ```

3. **Storage link oluşturma:**
   ```bash
   php artisan storage:link
   ```

### 2.4. İzinleri Ayarlama

```bash
chmod -R 755 laravel/
chmod -R 777 laravel/storage/
chmod -R 777 laravel/bootstrap/cache/
chmod 644 public_html/.htaccess
```

### 2.5. Cache ve Optimizasyon

```bash
cd laravel/
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 🔐 3. GÜVENLİK AYARLARI

### 3.1. Çevre Dosyası Güvenliği
- `.env` dosyasının web'den erişilemez olduğundan emin olun
- Veritabanı şifrelerini güçlü yapın

### 3.2. SSL Kurulumu
- Hosting panelinden SSL sertifikası aktif edin
- HTTPS yönlendirmesi ayarlayın

### 3.3. Firewall Ayarları
- Gereksiz portları kapatın
- IP bazlı erişim kısıtlamaları (opsiyonel)

## 📧 4. MAİL AYARLARI

### SMTP Konfigürasyonu:
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_strong_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Pazaryeri"
```

## 🎯 5. SON KONTROLLER

### Test Edilecek Özellikler:
- [ ] Ana sayfa yükleniyor
- [ ] Kullanıcı kayıt/giriş sistemi
- [ ] E-posta doğrulama
- [ ] Ürün listeleme ve detay
- [ ] Sepet sistemi (session + database)
- [ ] Ödeme sistemi (PayThor)
- [ ] Admin paneli erişimi
- [ ] Satıcı paneli erişimi
- [ ] Dosya yükleme (ürün resimleri)
- [ ] SSL sertifikası aktif

## 🚨 6. SORUN GİDERME

### Yaygın Hatalar ve Çözümleri:

**500 Internal Server Error:**
- Storage klasörü izinlerini kontrol edin (777)
- .env dosyasının doğru olduğundan emin olun
- Error log'larını inceleyin

**Database Connection Error:**
- Veritabanı bilgilerini kontrol edin
- MySQL servisinin aktif olduğundan emin olun

**Permission Denied:**
- Dosya izinlerini yeniden ayarlayın
- Apache/Nginx kullanıcı gruplarını kontrol edin

**404 Not Found:**
- .htaccess dosyasının doğru olduğundan emin olun
- mod_rewrite modülünün aktif olduğunu kontrol edin

## 📞 7. DESTEK

Bu rehberdeki adımları takip ettikten sonra sorun yaşarsanız:
1. Hosting sağlayıcınızın PHP/MySQL versiyonlarını kontrol edin
2. Error log dosyalarını inceleyin
3. Laravel debug modunu geçici olarak açın (.env APP_DEBUG=true)

---
**🎉 Başarılı kurulum sonrası sisteminiz production'da çalışacaktır!**
