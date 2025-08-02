<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kullanici;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationOtpMail;
use Carbon\Carbon;
use App\Models\Siparis;
use App\Models\Sepet;
use App\Models\Urun;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'eposta' => 'required|email',
            'sifre' => 'required',
        ]);

        $kullanici = Kullanici::where('eposta', $request->eposta)->first();

        if ($kullanici && Hash::check($request->sifre, $kullanici->sifre)) {
            // OTP doğrulaması şimdilik devre dışı
            // if ($kullanici->rol !== 'yonetici' && !$kullanici->email_verified) {
            //     return back()->withErrors(['eposta' => 'E-posta adresinizi doğrulamanız gerekiyor.']);
            // }
            
            Auth::login($kullanici);
            
            // Session'daki sepeti veritabanına aktar
            $this->transferSessionCartToDatabase($kullanici->id);
            
            // Session verilerini ayarla
            Session::put('kullanici_id', $kullanici->id);
            Session::put('kullanici_adi', $kullanici->ad);
            Session::put('kullanici_rol', $kullanici->rol);
            
            // Session'ı kaydet
            Session::save();

            // Rol'e göre yönlendirme
            if ($kullanici->rol === 'yonetici') {
                return redirect()->route('admin.dashboard');
            } elseif ($kullanici->rol === 'satici') {
                return redirect()->route('satici.dashboard');
            } else {
                return redirect()->route('home');
            }
        }

        return back()->withErrors(['eposta' => 'Giriş bilgileri hatalı!']);
    }

    public function register()
    {
        return view('auth.register');
    }

    public function registerPost(Request $request)
    {
        $request->validate([
            'ad' => 'required|string|max:255',
            'eposta' => 'required|email|unique:kullanicis,eposta',
            'sifre' => 'required|min:6|confirmed',
            'sifre_confirmation' => 'required',
            'rol' => 'required|in:musteri,satici',
        ], [
            'sifre.confirmed' => 'Şifreler eşleşmiyor.',
            'sifre_confirmation.required' => 'Şifre tekrarı zorunludur.',
        ]);

        // OTP devre dışı - direkt kullanıcı oluştur
        $kullanici = Kullanici::create([
            'ad' => $request->ad,
            'eposta' => $request->eposta,
            'sifre' => Hash::make($request->sifre),
            'rol' => $request->rol,
            'email_verified' => true,
            'email_verified_at' => Carbon::now(),
        ]);

        // Kullanıcıyı sisteme giriş yap
        Auth::login($kullanici);
        
        // Session'daki sepeti veritabanına aktar
        $this->transferSessionCartToDatabase($kullanici->id);
        
        Session::put('kullanici_id', $kullanici->id);
        Session::put('kullanici_adi', $kullanici->ad);
        Session::put('kullanici_rol', $kullanici->rol);
        Session::save();

        // Rol'e göre yönlendirme
        if ($kullanici->rol === 'satici') {
            return redirect()->route('satici.dashboard')->with('success', 'Kayıt başarılı! Hoş geldiniz.');
        } else {
            return redirect()->route('home')->with('success', 'Kayıt başarılı! Hoş geldiniz.');
        }
    }

    public function adminRegister()
    {
        // Veritabanında admin var mı kontrol et (cache ile)
        $adminSayisi = Cache::remember('admin_count', 60, function () {
            return Kullanici::where('rol', 'yonetici')->count();
        });
        
        if ($adminSayisi > 0) {
            return redirect()->route('home')->with('error', 'Admin kaydı kapalıdır.');
        }
        
        return view('auth.admin-register');
    }

    public function adminRegisterPost(Request $request)
    {
        // Veritabanında admin var mı kontrol et (cache ile)
        $adminSayisi = Cache::remember('admin_count', 60, function () {
            return Kullanici::where('rol', 'yonetici')->count();
        });
        
        if ($adminSayisi > 0) {
            return redirect()->route('home')->with('error', 'Admin kaydı kapalıdır.');
        }
        
        // Gizli kod kontrolü (environment'den al)
        $requiredCode = env('ADMIN_SECRET_CODE', 'default_admin_code');
        if ($request->secret_code !== $requiredCode) {
            return back()->withErrors(['secret_code' => 'Gizli kod hatalı!']);
        }

        $request->validate([
            'ad' => 'required|string|max:255',
            'eposta' => 'required|email|unique:kullanicis,eposta',
            'sifre' => 'required|min:6|confirmed',
        ]);

        $kullanici = Kullanici::create([
            'ad' => $request->ad,
            'eposta' => $request->eposta,
            'sifre' => Hash::make($request->sifre),
            'rol' => 'yonetici',
            'email_verified' => true,
            'email_verified_at' => Carbon::now(),
        ]);

        // Admin oluşturulduğunda cache'i temizle
        Cache::forget('admin_count');

        Auth::login($kullanici);
        Session::put('kullanici_id', $kullanici->id);
        Session::put('kullanici_adi', $kullanici->ad);
        Session::put('kullanici_rol', $kullanici->rol);
        Session::save();

        return redirect()->route('admin.dashboard')->with('success', 'Yönetici hesabınız başarıyla oluşturuldu!');
    }

    public function verifyEmail()
    {
        // Eğer doğrulama bekleyen kullanıcı verisi yoksa anasayfaya yönlendir
        if (!Session::has('pending_user_data')) {
            return redirect()->route('home')->with('error', 'Geçersiz erişim.');
        }

        return view('auth.verify-email');
    }

    public function verifyEmailPost(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        // Session'dan kullanıcı verilerini al
        $kullaniciData = Session::get('pending_user_data');
        if (!$kullaniciData) {
            return redirect()->route('home')->with('error', 'Geçersiz erişim.');
        }

        // OTP kodunu ve süresini kontrol et
        if ($kullaniciData['email_verification_token'] !== $request->otp) {
            return back()->withErrors(['otp' => 'Geçersiz doğrulama kodu!']);
        }

        if (Carbon::now()->isAfter($kullaniciData['email_verification_token_expires_at'])) {
            return back()->withErrors(['otp' => 'Doğrulama kodu süresi dolmuş! Yeni kod talep edin.']);
        }

        // Artık kullanıcıyı veritabanına kaydet
        $kullanici = Kullanici::create([
            'ad' => $kullaniciData['ad'],
            'eposta' => $kullaniciData['eposta'],
            'sifre' => $kullaniciData['sifre'],
            'rol' => $kullaniciData['rol'],
            'email_verified' => true,
            'email_verified_at' => Carbon::now(),
            'email_verification_token' => null,
            'email_verification_token_expires_at' => null,
        ]);

        // Kullanıcıyı sisteme giriş yap
        Auth::login($kullanici);
        
        // Session'daki sepeti veritabanına aktar
        $this->transferSessionCartToDatabase($kullanici->id);
        
        Session::put('kullanici_id', $kullanici->id);
        Session::put('kullanici_adi', $kullanici->ad);
        Session::put('kullanici_rol', $kullanici->rol);
        Session::forget('pending_user_data');
        Session::save();

        return redirect()->route('home')->with('success', 'E-posta adresiniz başarıyla doğrulandı! Hoş geldiniz.');
    }

    public function resendOtp()
    {
        // Session'dan kullanıcı verilerini al
        $kullaniciData = Session::get('pending_user_data');
        if (!$kullaniciData) {
            return redirect()->route('home')->with('error', 'Geçersiz erişim.');
        }

        // Yeni OTP kodu oluştur
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpExpiresAt = Carbon::now()->addMinutes(10);

        // Session'daki OTP'yi güncelle
        $kullaniciData['email_verification_token'] = $otp;
        $kullaniciData['email_verification_token_expires_at'] = $otpExpiresAt;
        Session::put('pending_user_data', $kullaniciData);

        // Yeni OTP kodunu e-posta ile gönder
        try {
            $mailInstance = new \App\Mail\VerificationOtpMail($otp, $kullaniciData['ad']);
            Mail::to($kullaniciData['eposta'])->send($mailInstance);
            return back()->with('success', 'Yeni doğrulama kodu e-posta adresinize gönderildi.');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'E-posta gönderilirken hata oluştu: ' . $e->getMessage()]);
        }
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('home');
    }

    /**
     * Profil sayfasını göster
     */
    public function profil()
    {
        $kullanici = Auth::user();
        
        // İstatistikleri hesapla
        $stats = [];
        
        if (Session::get('kullanici_rol') === 'musteri') {
            // Müşteri istatistikleri
            $stats['toplam_siparis'] = Siparis::where('kullanici_id', $kullanici->id)->count();
            $stats['toplam_harcama'] = Siparis::where('kullanici_id', $kullanici->id)
                ->where('durum', '!=', 'iptal')
                ->sum('toplam_tutar');
            $stats['sepetdeki_urun'] = Sepet::where('kullanici_id', $kullanici->id)->sum('miktar');
            
            // Son siparişler
            $son_siparisler = Siparis::with(['siparisUrunleri.urun'])
                ->where('kullanici_id', $kullanici->id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
            $stats['son_siparisler'] = $son_siparisler;
        }
        
        if (Session::get('kullanici_rol') === 'satici') {
            // Satıcı istatistikleri - mağaza ID'sini bul
            $magaza = \App\Models\Magaza::where('kullanici_id', $kullanici->id)->first();
            if ($magaza) {
                $stats['aktif_urun'] = \App\Models\Urun::where('magaza_id', $magaza->id)->count();
                $stats['toplam_satis'] = 0; // Bu hesaplanabilir
                $stats['bekleyen_siparis'] = 0; // Bu hesaplanabilir
            }
        }
        
        return view('profil', compact('kullanici', 'stats'));
    }

    public function profilDuzenle()
    {
        $kullanici = Auth::user();
        return view('auth.profil-duzenle', compact('kullanici'));
    }

    public function profilDuzenlePost(Request $request)
    {
        $kullanici = Auth::user();
        
        $request->validate([
            'ad' => 'required|string|max:255',
            'eposta' => 'required|email|unique:kullanicis,eposta,' . $kullanici->id,
            'telefon' => 'nullable|string|max:20',
            'adres' => 'nullable|string|max:500',
        ]);

        $kullanici->update([
            'ad' => $request->ad,
            'eposta' => $request->eposta,
            'telefon' => $request->telefon,
            'adres' => $request->adres,
        ]);

        // Session güncelle
        Session::put('kullanici_adi', $kullanici->ad);
        Session::save();

        return redirect()->route('profil')->with('success', 'Profil bilgileriniz başarıyla güncellendi!');
    }

    public function sifreDegistir()
    {
        return view('auth.sifre-degistir');
    }

    public function sifreDegistirPost(Request $request)
    {
        $request->validate([
            'mevcut_sifre' => 'required',
            'yeni_sifre' => 'required|min:6|confirmed',
        ]);

        $kullanici = Auth::user();

        // Mevcut şifre doğrulaması
        if (!Hash::check($request->mevcut_sifre, $kullanici->sifre)) {
            return back()->withErrors(['mevcut_sifre' => 'Mevcut şifreniz hatalı!']);
        }

        // Yeni şifre güncellemesi
        $kullanici->update([
            'sifre' => Hash::make($request->yeni_sifre)
        ]);

        return redirect()->route('profil')->with('success', 'Şifreniz başarıyla değiştirildi!');
    }

    /**
     * Kullanıcının siparişlerini listele
     */
    public function siparislerim()
    {
        $kullanici = Auth::user();
        
        // Kullanıcının siparişlerini getir (en yeni önce)
        $siparisler = Siparis::with(['siparisUrunleri.urun'])
            ->where('kullanici_id', $kullanici->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('auth.siparislerim', compact('siparisler'));
    }

    /**
     * Session'daki sepeti veritabanına aktar
     */
    private function transferSessionCartToDatabase($kullaniciId)
    {
        try {
            $sessionSepet = Session::get('sepet', []);
            
            if (!empty($sessionSepet)) {
                foreach ($sessionSepet as $urunId => $miktar) {
                    // Ürünün var olduğunu kontrol et
                    $urun = Urun::find($urunId);
                    if (!$urun) continue;
                    
                    // Veritabanında bu ürün var mı kontrol et
                    $existingSepetItem = Sepet::where('kullanici_id', $kullaniciId)
                        ->where('urun_id', $urunId)
                        ->first();
                    
                    if ($existingSepetItem) {
                        // Varsa miktarı topla ama stok sınırını aşma
                        $yeniMiktar = min($existingSepetItem->miktar + $miktar, $urun->stok);
                        $existingSepetItem->miktar = $yeniMiktar;
                        $existingSepetItem->save();
                    } else {
                        // Yoksa yeni ekle ama stok sınırını aşma
                        $yeniMiktar = min($miktar, $urun->stok);
                        Sepet::create([
                            'kullanici_id' => $kullaniciId,
                            'urun_id' => $urunId,
                            'miktar' => $yeniMiktar
                        ]);
                    }
                }
                
                // Session sepetini temizle
                Session::forget('sepet');
            }
        } catch (\Exception $e) {
            \Log::error('Sepet aktarma hatası: ' . $e->getMessage());
        }
    }
}
