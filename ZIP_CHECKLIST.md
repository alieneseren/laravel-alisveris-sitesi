# Laravel Pazaryeri - Hosting Upload Checklist

## 📦 ZIP Paketleme Kontrol Listesi

### ✅ Dahil Edilecek Klasörler:
- [ ] app/
- [ ] bootstrap/
- [ ] config/
- [ ] database/
- [ ] public/ (uploads/ dahil)
- [ ] resources/
- [ ] routes/
- [ ] storage/
- [ ] tests/

### ✅ Dahil Edilecek Dosyalar:
- [ ] .htaccess
- [ ] artisan
- [ ] composer.json (ÖNEMLİ!)
- [ ] composer.lock (ÖNEMLİ!)
- [ ] package.json
- [ ] phpunit.xml
- [ ] vite.config.js
- [ ] README.md
- [ ] .env.production (hosting'de .env olarak yeniden adlandır)

### ❌ ASLA Dahil Edilmeyecekler:
- [ ] vendor/ klasörü (300MB+)
- [ ] .env (local ayarlar)
- [ ] node_modules/ (eğer varsa)
- [ ] .git/ klasörü
- [ ] storage/logs/*.log (eski loglar)
- [ ] .DS_Store (Mac)
- [ ] Thumbs.db (Windows)

### 🎯 Sonuç:
- Hedef Boyut: ~10-50 MB (vendor olmadan)
- Upload Süresi: 1-5 dakika
- Hosting Setup: composer install ile vendor oluştur

### 📝 Hosting Notları:
1. ZIP upload sonrası vendor/ yok - NORMAL!
2. composer install çalıştır
3. .env.production → .env olarak yeniden adlandır
4. Database import et
5. Laravel optimize komutları çalıştır
