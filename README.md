# 🛒 Laravel Pazaryeri - E-Ticaret Platformu

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 Proje Özeti

**Laravel Pazaryeri**, modern Laravel 12.x framework'ü kullanılarak geliştirilmiş kapsamlı bir **multi-vendor e-ticaret platformudur**. Platform, hem alıcılar hem de satıcılar için optimize edilmiş kullanıcı dostu bir deneyim sunar.

### 🎯 Ana Özellikler

- **🛍️ Multi-Vendor Marketplace** - Çoklu satıcı desteği
- **👥 Role-Based System** - Kullanıcı, Satıcı, Yönetici rolleri
- **🛒 Sepet Sistemi** - Misafir ve kayıtlı kullanıcı sepeti
- **💳 PayThor Payment** - Güvenli ödeme entegrasyonu
- **🏪 Mağaza Yönetimi** - Satıcı panel sistemi
- **📱 Responsive Design** - Mobil uyumlu arayüz
- **🔐 Güvenlik** - Rate limiting, CSRF koruması
- **📧 E-mail System** - SMTP mail entegrasyonu
- **📊 Admin Panel** - Kapsamlı yönetim paneli
- **🔍 Arama & Filtreleme** - Gelişmiş ürün arama

### 🏗️ Teknik Altyapı

- **Framework:** Laravel 12.x (PHP 8.2+)
- **Database:** MySQL 8.0+
- **Authentication:** Custom Kullanici Model
- **Session:** File-based storage
- **Cache:** Database driver
- **Mail:** SMTP (Gmail/Custom)
- **Payment:** PayThor Gateway
- **Frontend:** Blade Templates + CSS/JS

---

## 🚀 Lokal Kurulum Rehberi

### 📋 Sistem Gereksinimleri

- **PHP** 8.2 veya üzeri
- **Composer** 2.5+
- **MySQL** 8.0+ veya MariaDB 10.3+
- **Node.js** 18+ (opsiyonel, frontend asset'ler için)
- **Git** (versiyon kontrol)

### 🔧 Adım Adım Kurulum

#### 1️⃣ Proje Klonlama
```bash
# Repository'yi klonla
git clone https://github.com/alieneseren/laravel-alisveris-sitesi.git
cd laravel-alisveris-sitesi

# veya ZIP indirip çıkart
# unzip laravel-alisveris-sitesi.zip
# cd laravel-alisveris-sitesi
```

#### 2️⃣ Bağımlılıkları Yükle
```bash
# PHP bağımlılıkları
composer install

# Node.js bağımlılıkları (opsiyonel)
npm install
```

#### 3️⃣ Environment Konfigürasyonu
```bash
# .env dosyasını oluştur
cp .env.example .env

# Laravel application key oluştur
php artisan key:generate
```

#### 4️⃣ .env Dosyasını Düzenle
`.env` dosyasında aşağıdaki ayarları yapın:

```env
# Uygulama Ayarları
APP_NAME=Pazaryeri
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Veritabanı Ayarları
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pazaryeri2
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

# Mail Ayarları (opsiyonel)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="Pazaryeri"

# Admin Güvenlik
ADMIN_SECRET_CODE=ADMIN2025_SUPER_SECRET_KEY_2025
```

#### 5️⃣ Veritabanı Kurulumu

**MySQL/MariaDB'de veritabanı oluştur:**
```sql
-- MySQL Command Line veya phpMyAdmin'de
CREATE DATABASE pazaryeri2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Migration'ları çalıştır:**
```bash
# Veritabanı tablolarını oluştur
php artisan migrate

# Tablo ilişkilerini kontrol et (foreign key constraints)
php artisan tinker
# Tinker içinde test sorguları:
# \DB::select('SHOW CREATE TABLE uruns');
# \DB::select('SHOW CREATE TABLE siparis');

# Örnek verileri yükle (opsiyonel)
php artisan db:seed
```

#### 6️⃣ Storage Link Oluştur
```bash
# Public storage link'i oluştur (görseller için)
php artisan storage:link
```

#### 7️⃣ Dosya İzinleri (Linux/Mac)
```bash
# Storage ve cache klasörleri için yazma izni
chmod -R 775 storage
chmod -R 775 bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

#### 8️⃣ Frontend Asset'leri (Opsiyonel)
```bash
# Development için
npm run dev

# Production için
npm run build
```

#### 9️⃣ Sunucuyu Başlat
```bash
# Laravel development server
php artisan serve

# Alternatif port
php artisan serve --port=8080
```

#### 🔟 Upload Klasörü Oluştur
```bash
# Ürün görselleri için klasör oluştur
mkdir -p public/uploads/urunler
chmod 775 public/uploads/urunler
```

---

## 📊 Veritabanı Şeması

### Ana Tablolar
```sql
- kullanicis          # Kullanıcı bilgileri (rol tabanlı)
- kategoris           # Ürün kategorileri (hiyerarşik)
- magazas             # Satıcı mağazaları
- uruns               # Ürün bilgileri
- siparis             # Sipariş yönetimi
- siparis_urunus      # Sipariş detayları
- sepets              # Sepet (guest + auth)
- urun_gorsels        # Ürün görselleri
- urun_yorumus        # Ürün yorumları
```

### 🔗 Veritabanı İlişkileri

**Foreign Key Constraints:**
```sql
-- Ürünler tablosu ilişkileri
uruns.kullanici_id → kullanicis.id (Satıcı)
uruns.kategori_id → kategoris.id (Kategori)

-- Sipariş ilişkileri
siparis.kullanici_id → kullanicis.id (Müşteri)
siparis_urunus.siparis_id → siparis.id (Sipariş)
siparis_urunus.urun_id → uruns.id (Ürün)

-- Sepet ilişkileri
sepets.kullanici_id → kullanicis.id (nullable, guest sepet için)
sepets.urun_id → uruns.id (Ürün)

-- Mağaza ilişkileri
magazas.kullanici_id → kullanicis.id (Satıcı)

-- Ürün görselleri
urun_gorsels.urun_id → uruns.id (Ürün)

-- Yorumlar
urun_yorumus.urun_id → uruns.id (Ürün)
urun_yorumus.kullanici_id → kullanicis.id (Yorum yapan)
```

**İlişki Kontrolü:**
```bash
# Migration'lar çalıştırıldıktan sonra ilişkileri kontrol et
php artisan tinker

# Foreign key constraint'leri listele:
\DB::select("SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE REFERENCED_TABLE_SCHEMA = 'pazaryeri2' 
AND REFERENCED_TABLE_NAME IS NOT NULL");
```

### Demo Data Oluşturma
```bash
# Test kullanıcıları ve ürünler oluştur
php artisan tinker

# Tinker içinde:
App\Models\Kullanici::factory(10)->create();
App\Models\Urun::factory(50)->create();

# İlişkili verilerle test:
$kullanici = App\Models\Kullanici::factory()->create(['rol' => 'satici']);
$magaza = App\Models\Magaza::create(['kullanici_id' => $kullanici->id, 'magaza_adi' => 'Test Mağaza']);
$urun = App\Models\Urun::factory()->create(['kullanici_id' => $kullanici->id]);
```

**Manuel İlişki Oluşturma:**
```bash
# Eğer migration'larda foreign key eksikse manuel oluştur:
php artisan tinker

# Foreign key constraint'leri ekle:
\DB::statement('ALTER TABLE uruns ADD CONSTRAINT fk_uruns_kullanici FOREIGN KEY (kullanici_id) REFERENCES kullanicis(id) ON DELETE CASCADE');
\DB::statement('ALTER TABLE uruns ADD CONSTRAINT fk_uruns_kategori FOREIGN KEY (kategori_id) REFERENCES kategoris(id) ON DELETE SET NULL');
\DB::statement('ALTER TABLE siparis ADD CONSTRAINT fk_siparis_kullanici FOREIGN KEY (kullanici_id) REFERENCES kullanicis(id) ON DELETE CASCADE');
```

---

## 🎮 Kullanım Rehberi

### 🌐 Erişim URL'leri

| Sayfa | URL | Açıklama |
|-------|-----|----------|
| **Ana Sayfa** | `http://localhost:8000` | Ürün listesi ve kategoriler |
| **Giriş** | `http://localhost:8000/login` | Kullanıcı girişi |
| **Kayıt** | `http://localhost:8000/register` | Yeni kullanıcı kaydı |
| **Admin Kayıt** | `http://localhost:8000/admin/register` | Admin kaydı (gizli kod ile) |
| **Sepet** | `http://localhost:8000/sepet` | Alışveriş sepeti |
| **Ürünler** | `http://localhost:8000/urunler` | Tüm ürünler |
| **Admin Panel** | `http://localhost:8000/admin` | Yönetim paneli |
| **Satıcı Panel** | `http://localhost:8000/satici` | Satıcı yönetimi |

### 👤 Test Kullanıcıları

**Admin Hesabı Oluşturma:**
1. `http://localhost:8000/admin/register` adresine git
2. Gizli kod: `ADMIN2025_SUPER_SECRET_KEY_2025`
3. Admin bilgilerini gir ve kayıt ol

**Normal Kullanıcı:**
1. `http://localhost:8000/register` adresine git
2. Kullanıcı bilgilerini gir
3. E-mail doğrulaması şu anda devre dışı

### 🛒 E-Ticaret İşlemleri

**Ürün Ekleme (Satıcı):**
1. Satıcı olarak giriş yap
2. Satıcı paneline git
3. "Ürün Ekle" seçeneğini kullan
4. Ürün bilgileri ve görselleri yükle

**Sipariş Verme:**
1. Ürünleri sepete ekle
2. Sepeti kontrol et
3. PayThor ile ödeme yap
4. Sipariş onayını bekle

---

## 🔧 Geliştirme Araçları

### Artisan Komutları
```bash
# Cache temizleme
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizasyon
php artisan optimize
php artisan config:cache
php artisan route:cache

# Database işlemleri
php artisan migrate:fresh --seed
php artisan migrate:rollback

# Tinker (Laravel console)
php artisan tinker
```

### Debug & Logging
```bash
# Log dosyalarını takip et
tail -f storage/logs/laravel.log

# Debug bilgileri için .env'de
APP_DEBUG=true
LOG_LEVEL=debug
```

---

## � GitHub'a Yükleme Rehberi

### 🔧 İlk Kurulum (Yeni Repository)

**1. GitHub'da Repository Oluştur:**
1. [GitHub.com](https://github.com)'a git
2. "New repository" butonuna tıkla
3. Repository name: `laravel-alisveris-sitesi`
4. Description: `Laravel tabanlı multi-vendor e-ticaret platformu`
5. Public/Private seç
6. "Create repository" tıkla

**2. Lokal Git Kurulumu:**
```bash
# Git repository başlat
git init

# Uzak repository ekle
git remote add origin https://github.com/alieneseren/laravel-alisveris-sitesi.git

# Ana branch'i main olarak ayarla
git branch -M main
```

**3. .gitignore Dosyasını Kontrol Et:**
```bash
# .gitignore içeriğini kontrol et
cat .gitignore

# Eksikse ekle:
echo "vendor/" >> .gitignore
echo ".env" >> .gitignore
echo "node_modules/" >> .gitignore
echo "storage/logs/*.log" >> .gitignore
echo "public/uploads/*" >> .gitignore
echo "!public/uploads/.gitkeep" >> .gitignore
```

**4. İlk Commit ve Push:**
```bash
# Tüm dosyaları staging'e ekle
git add .

# İlk commit
git commit -m "🎉 İlk commit: Laravel Pazaryeri projesi"

# GitHub'a push et
git push -u origin main
```

### 🔄 Güncellemeleri Gönderme

```bash
# Değişiklikleri kontrol et
git status

# Dosyaları staging'e ekle
git add .
# veya belirli dosyalar için:
git add README.md app/Http/Controllers/

# Commit yap
git commit -m "✨ Yeni özellik: Ürün yorumları eklendi"

# GitHub'a gönder
git push origin main
```

### 🛡️ Güvenlik Kontrolleri

**Hassas Bilgileri Kaldır:**
```bash
# .env dosyasının push edilmediğini kontrol et
git ls-files | grep .env

# Eğer .env push edildiyse geçmişten sil:
git filter-branch --index-filter 'git rm --cached --ignore-unmatch .env' HEAD
git push --force-with-lease origin main
```

**Örnek .env.example Oluştur:**
```bash
# .env.example dosyası oluştur
cp .env .env.example

# Hassas bilgileri temizle
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=your_password/' .env.example
sed -i 's/MAIL_PASSWORD=.*/MAIL_PASSWORD=your_mail_password/' .env.example
sed -i 's/ADMIN_SECRET_CODE=.*/ADMIN_SECRET_CODE=your_secret_code/' .env.example

# Commit et
git add .env.example
git commit -m "📝 .env.example dosyası eklendi"
git push origin main
```

### 📋 Repository README Güncellemesi

**GitHub Repository Ayarları:**
1. Repository "Settings" → "General"
2. Features bölümünde "Issues", "Wiki" aktifleştir
3. "Social Preview" için logo yükle
4. "Topics" ekle: `laravel`, `e-commerce`, `php`, `mysql`, `marketplace`

### �🚀 Production Deployment

### Hosting Hazırlığı
```bash
# Production optimizasyonu
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# .env production ayarları
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### Güvenlik Kontrolleri
- ✅ `APP_DEBUG=false` 
- ✅ `APP_ENV=production`
- ✅ HTTPS sertifikası
- ✅ `.env` dosya izinleri (600)
- ✅ Storage klasör izinleri (755)
- ✅ Admin gizli kodu güçlendirme

---

## 🛠️ Sorun Giderme

### Yaygın Hatalar

**1. Composer Install Hatası**
```bash
# Composer cache temizle
composer clear-cache
composer install --no-cache
```

**2. Storage Permission Hatası**
```bash
# Linux/Mac
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache

# Windows (Admin CMD)
icacls storage /grant Everyone:(OI)(CI)F /T
```

**3. Database Connection Hatası**
```bash
# MySQL servisini kontrol et
sudo systemctl status mysql

# .env database bilgilerini kontrol et
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pazaryeri2
```

**4. Artisan Komut Hatası**
```bash
# Autoload dosyalarını yenile
composer dump-autoload
php artisan clear-compiled
```

**5. 500 Internal Error**
```bash
# Log dosyasını kontrol et
tail -f storage/logs/laravel.log

# Debug mode aç
APP_DEBUG=true (sadece development)
```

### Performance İyileştirmesi
```bash
# OPcache aktifleştir (production)
# php.ini'de:
opcache.enable=1
opcache.memory_consumption=256

# Queue worker çalıştır
php artisan queue:work --daemon
```

---

## 📁 Proje Yapısı

```
laravel-pazaryeri/
├── app/
│   ├── Http/
│   │   ├── Controllers/         # MVC Controllers
│   │   └── Middleware/          # Custom middleware
│   ├── Models/                  # Eloquent models
│   └── Mail/                    # Mail classes
├── config/                      # Konfigürasyon dosyaları
├── database/
│   ├── migrations/              # Veritabanı şeması
│   └── seeders/                 # Test verileri
├── public/
│   ├── uploads/                 # Yüklenen dosyalar
│   └── assets/                  # CSS, JS, images
├── resources/
│   └── views/                   # Blade şablonları
├── routes/
│   └── web.php                  # Web rotaları
├── storage/                     # Cache, logs, sessions
├── .env                         # Environment variables
├── composer.json                # PHP bağımlılıkları
└── artisan                      # Laravel CLI
```

---

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add amazing feature'`)
4. Branch'i push edin (`git push origin feature/amazing-feature`)
5. Pull Request açın

---

## 📞 Destek & İletişim

- **Geliştirici:** Ali Enes Eren
- **E-mail:** alienes.eren3024@gop.edu.tr
- **GitHub:** [GitHub Profile](https://github.com/alieneseren)
- **Repository:** [laravel-alisveris-sitesi](https://github.com/alieneseren/laravel-alisveris-sitesi)


---

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

---

## 🙏 Teşekkürler

- [Laravel Framework](https://laravel.com)
- [PayThor Payment Gateway](https://paythor.com)
- Tüm açık kaynak katkıda bulunanlara

---

**🚀 Happy Coding!** ✨
