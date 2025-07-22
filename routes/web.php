<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UrunController;
use App\Http\Controllers\SepetController;
use App\Http\Controllers\SaticiController;

// Ana sayfa ve public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kategori/{id}', [HomeController::class, 'kategori'])->name('kategori');
Route::get('/arama', [HomeController::class, 'arama'])->name('arama');

// Sepet count route (auth kontrolü controller'da)
Route::get('/sepet/count', [SepetController::class, 'count'])->name('sepet.count');

// Mağaza profil route
Route::get('/magaza/{id}', function($id) {
    return redirect()->route('urun.index');
})->name('magaza.profil');

// Ürün routes
Route::get('/urunler', [UrunController::class, 'index'])->name('urun.index');
Route::get('/urun/{id}', [UrunController::class, 'show'])->name('urun.detay');
Route::post('/urun/{id}/yorum', [UrunController::class, 'yorumEkle'])->name('urun.yorum.ekle');

// Auth routes (Rate limiting ile korumalı)
Route::middleware(['throttle:5,1'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerPost'])->name('register.post');
    Route::get('/admin/register', [AuthController::class, 'adminRegister'])->name('admin.register');
    Route::post('/admin/register', [AuthController::class, 'adminRegisterPost'])->name('admin.register.post');
});

// OTP routes (şimdilik devre dışı)
// Route::get('/verify-email', [AuthController::class, 'verifyEmail'])->name('verify-email');
// Route::post('/verify-email', [AuthController::class, 'verifyEmailPost'])->name('verify-email-post');
// Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend-otp');

Route::match(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Sepet routes (misafir kullanıcılar da erişebilir)
Route::get('/sepet', [SepetController::class, 'index'])->name('sepet');
Route::post('/sepet/ekle', [SepetController::class, 'ekle'])->name('sepet.ekle');
Route::delete('/sepet/{id}', [SepetController::class, 'cikar'])->name('sepet.cikar');
Route::post('/sepet/adet-guncelle', [SepetController::class, 'adetGuncelle'])->name('sepet.adet.guncelle');
Route::get('/sepet/odeme', [SepetController::class, 'odeme'])->name('sepet.odeme'); // Login kontrolü controller'da
Route::post('/sepet/odeme', [SepetController::class, 'odemeYap'])->name('sepet.odeme.yap'); // Login kontrolü controller'da

// PayThor ödeme sonuç sayfaları
Route::get('/odeme/basarili', [SepetController::class, 'odemeBasarili'])->name('odeme.basarili');
Route::get('/odeme/basarisiz', [SepetController::class, 'odemeBasarisiz'])->name('odeme.basarisiz');
Route::post('/paythor/callback', [SepetController::class, 'paythorCallback'])->name('paythor.callback');

// PayThor callback ve ödeme sonuç sayfaları
Route::post('/paythor/callback', [SepetController::class, 'paythorCallback'])->name('paythor.callback');
Route::get('/odeme/basarili', [SepetController::class, 'odemeBasarili'])->name('odeme.basarili');
Route::get('/odeme/basarisiz', [SepetController::class, 'odemeBasarisiz'])->name('odeme.basarisiz');

// Korumalı routes
Route::middleware(['auth'])->group(function () {
    // Profil routes
    Route::get('/profil', function() {
        return view('profil');
    })->name('profil');
    Route::get('/profil/duzenle', [AuthController::class, 'profilDuzenle'])->name('profil.duzenle');
    Route::put('/profil/duzenle', [AuthController::class, 'profilDuzenlePost'])->name('profil.duzenle.post');
    Route::get('/sifre-degistir', [AuthController::class, 'sifreDegistir'])->name('sifre.degistir');
    Route::put('/sifre-degistir', [AuthController::class, 'sifreDegistirPost'])->name('sifre.degistir.post');
    Route::get('/siparislerim', [AuthController::class, 'siparislerim'])->name('siparislerim');
    
    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/kullanicilar', [AdminController::class, 'kullanicilar'])->name('admin.kullanicilar');
        Route::get('/kullanici/{id}', [AdminController::class, 'kullaniciGoruntule'])->name('admin.kullanici.goruntule');
        Route::get('/kullanici/{id}/duzenle', [AdminController::class, 'kullaniciDuzenle'])->name('admin.kullanici.duzenle');
        Route::put('/kullanici/{id}/duzenle', [AdminController::class, 'kullaniciDuzenlePost'])->name('admin.kullanici.duzenle.post');
        Route::delete('/kullanici/{id}/sil', [AdminController::class, 'kullaniciSil'])->name('admin.kullanici.sil');
        Route::get('/kategoriler', [AdminController::class, 'kategoriler'])->name('admin.kategoriler');
        Route::get('/urunler', [AdminController::class, 'urunler'])->name('admin.urunler');
        Route::get('/urun/{id}', [AdminController::class, 'urunGoruntule'])->name('admin.urun.goruntule');
        Route::get('/urun/{id}/duzenle', [AdminController::class, 'urunDuzenle'])->name('admin.urun.duzenle');
        Route::put('/urun/{id}/duzenle', [AdminController::class, 'urunDuzenlePost'])->name('admin.urun.duzenle.post');
        Route::delete('/urun/{id}/sil', [AdminController::class, 'urunSil'])->name('admin.urun.sil');
        Route::get('/siparisler', [AdminController::class, 'siparisler'])->name('admin.siparisler');
        Route::get('/magazalar', [AdminController::class, 'magazalar'])->name('admin.magazalar');
        Route::get('/magaza/{id}', [AdminController::class, 'magazaGoruntule'])->name('admin.magaza.goruntule');
        Route::get('/magaza/{id}/duzenle', [AdminController::class, 'magazaDuzenle'])->name('admin.magaza.duzenle');
        Route::put('/magaza/{id}/duzenle', [AdminController::class, 'magazaDuzenlePost'])->name('admin.magaza.duzenle.post');
        Route::delete('/magaza/{id}/sil', [AdminController::class, 'magazaSil'])->name('admin.magaza.sil');
        Route::get('/paythor-api', [AdminController::class, 'paythorApi'])->name('admin.paythor.api');
        Route::post('/paythor-api', [AdminController::class, 'paythorApiKaydet'])->name('admin.paythor.api.kaydet');
        Route::post('/paythor-api/otp', [AdminController::class, 'paythorOtpDogrula'])->name('admin.paythor.api.otp');
        Route::delete('/paythor-api', [AdminController::class, 'paythorApiSil'])->name('admin.paythor.api.sil');
    });
    
    // Satıcı routes
    Route::middleware(['satici'])->prefix('satici')->group(function () {
        Route::get('/dashboard', [SaticiController::class, 'dashboard'])->name('satici.dashboard');
        Route::get('/magaza/olustur', [SaticiController::class, 'magazaOlustur'])->name('satici.magaza.olustur');
        Route::post('/magaza/olustur', [SaticiController::class, 'magazaOlusturPost'])->name('satici.magaza.olustur.post');
        Route::get('/urunler', [SaticiController::class, 'urunler'])->name('satici.urunler');
        Route::get('/urun/ekle', [SaticiController::class, 'urunEkle'])->name('satici.urun.ekle');
        Route::post('/urun/ekle', [SaticiController::class, 'urunEklePost'])->name('satici.urun.ekle.post');
        Route::get('/urun/{id}/duzenle', [SaticiController::class, 'urunDuzenle'])->name('satici.urun.duzenle');
        Route::put('/urun/{id}/duzenle', [SaticiController::class, 'urunDuzenlePost'])->name('satici.urun.duzenle.post');
        Route::delete('/urun/{id}/sil', [SaticiController::class, 'urunSil'])->name('satici.urun.sil');
    });
});
