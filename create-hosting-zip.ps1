# Laravel Pazaryeri - Hosting Zip Creator
# Bu script vendor klasörü hariç tüm dosyaları zip'ler

# Çıktı zip dosyası
$zipPath = "C:\Users\Enes\Desktop\laravel-pazaryeri-hosting.zip"

# Ana proje klasörü
$projectPath = "C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri"

# Hariç tutulacak klasörler ve dosyalar
$excludeItems = @(
    "vendor",
    "node_modules",
    ".git",
    ".env",
    "*.log",
    ".DS_Store",
    "Thumbs.db"
)

Write-Host "🚀 Laravel Pazaryeri Hosting Zip Oluşturuluyor..." -ForegroundColor Green
Write-Host "📁 Kaynak: $projectPath" -ForegroundColor Yellow
Write-Host "📦 Hedef: $zipPath" -ForegroundColor Yellow

# Eğer zip zaten varsa sil
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
    Write-Host "🗑️ Eski zip dosyası silindi" -ForegroundColor Red
}

# Geçici klasör oluştur
$tempPath = [System.IO.Path]::GetTempPath() + "laravel-temp"
if (Test-Path $tempPath) {
    Remove-Item $tempPath -Recurse -Force
}
New-Item -ItemType Directory -Path $tempPath | Out-Null

Write-Host "📋 Dosyalar kopyalanıyor..." -ForegroundColor Blue

# Tüm dosyaları kopyala (vendor hariç)
$items = Get-ChildItem -Path $projectPath -Exclude $excludeItems
foreach ($item in $items) {
    $destPath = Join-Path $tempPath $item.Name
    if ($item.PSIsContainer) {
        Write-Host "📁 Kopyalanıyor: $($item.Name)" -ForegroundColor Cyan
        Copy-Item -Path $item.FullName -Destination $destPath -Recurse -Force
    } else {
        Write-Host "📄 Kopyalanıyor: $($item.Name)" -ForegroundColor White
        Copy-Item -Path $item.FullName -Destination $destPath -Force
    }
}

# .env.production'ı .env olarak kopyala
$envProdPath = Join-Path $projectPath ".env.production"
$envDestPath = Join-Path $tempPath ".env"
if (Test-Path $envProdPath) {
    Copy-Item -Path $envProdPath -Destination $envDestPath -Force
    Write-Host "🔧 .env.production → .env olarak kopyalandı" -ForegroundColor Green
}

Write-Host "🗜️ Zip dosyası oluşturuluyor..." -ForegroundColor Blue

# Zip oluştur
Compress-Archive -Path "$tempPath\*" -DestinationPath $zipPath -Force

# Geçici klasörü temizle
Remove-Item $tempPath -Recurse -Force

# Zip boyutunu hesapla
$zipSize = (Get-Item $zipPath).Length / 1MB
$zipSizeRounded = [Math]::Round($zipSize, 2)

Write-Host "✅ ZIP OLUŞTURULDU!" -ForegroundColor Green
Write-Host "📦 Dosya: $zipPath" -ForegroundColor Yellow
Write-Host "📊 Boyut: $zipSizeRounded MB" -ForegroundColor Yellow
Write-Host "🚀 Hosting'e upload etmeye hazır!" -ForegroundColor Green

Write-Host "`n📋 SONRAKI ADIMLAR:" -ForegroundColor Magenta
Write-Host "1. Bu zip dosyasını hosting'e upload edin" -ForegroundColor White
Write-Host "2. Hosting'de composer install çalıştırın" -ForegroundColor White
Write-Host "3. Database'i import edin" -ForegroundColor White
Write-Host "4. .env dosyasını hosting bilgileriyle güncelleyin" -ForegroundColor White
Write-Host "5. Laravel optimize komutlarını çalıştırın" -ForegroundColor White
