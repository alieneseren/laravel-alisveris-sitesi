# OTP Sistemini Geri Açma Rehberi

## 🔄 OTP'yi Tekrar Aktif Etmek İçin:

### 1. AuthController.php Değişiklikleri

**Login metodunda:**
```php
// Bu satırdaki comment'i kaldırın:
if ($kullanici->rol !== 'yonetici' && !$kullanici->email_verified) {
    return back()->withErrors(['eposta' => 'E-posta adresinizi doğrulamanız gerekiyor. Lütfen kayıt olurken gönderilen doğrulama kodunu girin.']);
}
```

**registerPost metodunu tamamen değiştirin:**
```php
public function registerPost(Request $request)
{
    $request->validate([
        'ad' => 'required|string|max:255',
        'eposta' => 'required|email|unique:kullanicis,eposta',
        'sifre' => 'required|min:6|confirmed',
        'rol' => 'required|in:musteri,satici',
    ]);

    // 6 haneli OTP kodu oluştur
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // OTP'nin geçerlilik süresini 10 dakika olarak ayarla
    $otpExpiresAt = Carbon::now()->addMinutes(10);

    // Kullanıcı bilgilerini session'da sakla
    $kullaniciData = [
        'ad' => $request->ad,
        'eposta' => $request->eposta,
        'sifre' => Hash::make($request->sifre),
        'rol' => $request->rol,
        'email_verification_token' => $otp,
        'email_verification_token_expires_at' => $otpExpiresAt,
        'email_verified' => false,
    ];

    // OTP kodunu e-posta ile gönder
    try {
        $mailInstance = new \App\Mail\VerificationOtpMail($otp, $request->ad);
        Mail::to($request->eposta)->send($mailInstance);
        
        Session::put('pending_user_data', $kullaniciData);
        
        return redirect()->route('verify-email')->with('success', 'Kayıt başarılı! E-posta adresinize gönderilen 6 haneli kodu girin.');
    } catch (\Exception $e) {
        return back()->withErrors(['eposta' => 'E-posta gönderilirken hata oluştu: ' . $e->getMessage()]);
    }
}
```

### 2. Routes Değişiklikleri (web.php)

**Bu satırları aktif edin:**
```php
Route::get('/verify-email', [AuthController::class, 'verifyEmail'])->name('verify-email');
Route::post('/verify-email', [AuthController::class, 'verifyEmailPost'])->name('verify-email-post');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend-otp');
```

### 3. Cache Temizleme
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### 4. Test
```bash
php artisan mail:test test@example.com
```

## 💡 Notlar:
- E-posta ayarlarınız zaten .env dosyasında mevcut
- VerificationOtpMail sınıfı zaten app/Mail/ klasöründe var
- verify-email.blade.php view dosyası zaten mevcut
- Tüm migration'lar email_verification alanlarını destekliyor

## 🚀 Hızlı Değiştirme:
Sadece AuthController.php ve web.php dosyalarını eski haline çevirin, route cache temizleyin - hazır!
