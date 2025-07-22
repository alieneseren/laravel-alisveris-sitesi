#!/bin/bash

# Pazaryeri Hosting Deployment Script
# Bu script hosting'e yükleme sonrası çalıştırılmalıdır

echo "🚀 Pazaryeri Hosting Deployment Başlatılıyor..."

# Gerekli dizinleri oluştur
echo "📁 Dizinler oluşturuluyor..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p storage/app/public/uploads

# İzinleri ayarla
echo "🔐 İzinler ayarlanıyor..."
chmod -R 755 ./
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/

# Composer bağımlılıklarını yükle
echo "📦 Composer bağımlılıkları yükleniyor..."
composer install --optimize-autoloader --no-dev

# Laravel ayarları
echo "⚙️ Laravel konfigürasyonu..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link oluştur
echo "🔗 Storage link oluşturuluyor..."
php artisan storage:link

# Database migration'ları çalıştır
echo "🗄️ Database migration'ları çalıştırılıyor..."
php artisan migrate --force

# Cache temizle ve optimize et
echo "🧹 Cache optimizasyonu..."
php artisan optimize

echo "✅ Deployment tamamlandı!"
echo "🌐 Sitenizi kontrol edebilirsiniz: https://yourdomain.com"
