# Docker MySQL Kurulum Rehberi

## 🐳 Docker ile MySQL Kurulumu

### 1. Docker Desktop Kontrolü
```bash
docker --version
docker ps
```

### 2. Docker Compose Dosyası (docker-compose.yml)
```yaml
services:
  mysql:
    image: mysql:8.0
    container_name: laravel_mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: pazaryeri2
      MYSQL_ROOT_PASSWORD: root
      MYSQL_PASSWORD: password  
      MYSQL_USER: laravel
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "3307:3306"
    networks:
      - laravel_network
    command: --default-authentication-plugin=mysql_native_password

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: laravel_phpmyadmin
    restart: unless-stopped
    depends_on:
      - mysql
    environment:
      PMA_HOST: mysql
      PMA_PORT: 3306
      PMA_USER: root
      PMA_PASSWORD: root
    ports:
      - "8080:80"
    networks:
      - laravel_network

volumes:
  mysql_data:
    driver: local

networks:
  laravel_network:
    driver: bridge
```

### 3. Container'ları Başlat
```bash
# MySQL'i başlat
docker-compose up -d mysql

# PHPMyAdmin'i de başlat
docker-compose up -d

# Container durumunu kontrol et
docker ps
```

### 4. Laravel .env Konfigürasyonu
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=pazaryeri2
DB_USERNAME=root
DB_PASSWORD=root
```

### 5. Migration ve Seed
```bash
php artisan migrate --force
php artisan db:seed --force
```

### 6. Erişim Noktaları
- **MySQL**: localhost:3307
- **PHPMyAdmin**: http://localhost:8080
- **Laravel**: http://127.0.0.1:8000

## 🛠️ Faydalı Komutlar

```bash
# Container'ları durdur
docker-compose down

# Logları görüntüle
docker-compose logs mysql

# MySQL shell'e bağlan
docker exec -it laravel_mysql mysql -u root -p

# Volume'ları da sil (dikkat: veriler silinir!)
docker-compose down -v
```

## ⚡ Hızlı Başlangıç

1. Docker Desktop'ı aç ve çalıştır
2. Terminal'de proje klasörüne git
3. `docker-compose up -d mysql` komutunu çalıştır  
4. Laravel .env dosyasını güncelle
5. `php artisan migrate --seed --force` çalıştır
6. Proje hazır!

## 🔧 Sorun Giderme

- **Docker Desktop çalışmıyor**: Uygulamayı yeniden başlat
- **Port çakışması**: .env'de 3307 yerine 3308 kullan
- **Container başlamıyor**: `docker-compose logs mysql` ile hataları kontrol et
- **Bağlantı sorunu**: `docker ps` ile container'ın çalıştığını doğrula
