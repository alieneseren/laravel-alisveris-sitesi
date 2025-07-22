@echo off
echo 🚀 Pazaryeri Hosting Deployment Baslatiliyor...

REM Gerekli dizinleri olustur
echo 📁 Dizinler olusturuluyor...
if not exist "storage\framework\sessions" mkdir storage\framework\sessions
if not exist "storage\framework\views" mkdir storage\framework\views
if not exist "storage\framework\cache" mkdir storage\framework\cache
if not exist "storage\logs" mkdir storage\logs
if not exist "storage\app\public\uploads" mkdir storage\app\public\uploads

REM Composer bagimliliklar
echo 📦 Composer bagimliliklari yukleniyor...
composer install --optimize-autoloader --no-dev

REM Laravel ayarlari
echo ⚙️ Laravel konfigurasyonu...
php artisan config:cache
php artisan route:cache
php artisan view:cache

REM Storage link olustur
echo 🔗 Storage link olusturuluyor...
php artisan storage:link

REM Database migrations
echo 🗄️ Database migrationlari calistiriliyor...
php artisan migrate --force

REM Cache temizle ve optimize et
echo 🧹 Cache optimizasyonu...
php artisan optimize

echo ✅ Deployment tamamlandi!
echo 🌐 Sitenizi kontrol edebilirsiniz
pause
