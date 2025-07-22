<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kullanici;
use App\Models\Urun;
use App\Models\Siparis;
use App\Models\Magaza;
use Illuminate\Support\Facades\Auth;
use App\Models\Kategori;

class AdminController extends Controller
{
    public function dashboard()
    {
        $istatistikler = [
            'toplam_kullanici' => Kullanici::count(),
            'toplam_urun' => Urun::count(),
            'toplam_siparis' => Siparis::count(),
            'toplam_magaza' => Magaza::count(),
        ];

        $son_siparisler = Siparis::with('kullanici')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $dusuk_stoklu_urunler = Urun::with('magaza')
            ->where('stok', '<=', 5)
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('istatistikler', 'son_siparisler', 'dusuk_stoklu_urunler'));
    }

    public function kullanicilar()
    {
        $kullanicilar = Kullanici::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.kullanicilar', compact('kullanicilar'));
    }

    public function kullaniciGoruntule($id)
    {
        $kullanici = Kullanici::findOrFail($id);
        return view('admin.kullanici_detay', compact('kullanici'));
    }

    public function kullaniciDuzenle($id)
    {
        $kullanici = Kullanici::findOrFail($id);
        return view('admin.kullanici_duzenle', compact('kullanici'));
    }

    public function kullaniciDuzenlePost(Request $request, $id)
    {
        $kullanici = Kullanici::findOrFail($id);
        
        $request->validate([
            'ad' => 'required|string|max:255',
            'eposta' => 'required|email|unique:kullanicis,eposta,' . $id,
            'rol' => 'required|in:yonetici,satici,musteri',
            'telefon' => 'nullable|string|max:20',
            'adres' => 'nullable|string|max:500',
        ]);

        $kullanici->update([
            'ad' => $request->ad,
            'eposta' => $request->eposta,
            'rol' => $request->rol,
            'telefon' => $request->telefon,
            'adres' => $request->adres,
        ]);

        return redirect()->route('admin.kullanicilar')->with('success', 'Kullanıcı başarıyla güncellendi!');
    }

    public function kullaniciSil($id)
    {
        $kullanici = Kullanici::findOrFail($id);
        
        // Yönetici silinemez
        if ($kullanici->rol === 'yonetici') {
            return redirect()->route('admin.kullanicilar')->with('error', 'Yönetici kullanıcılar silinemez!');
        }
        
        // Siparişi olan kullanıcı silinemez
        if ($kullanici->siparisler()->count() > 0) {
            return redirect()->route('admin.kullanicilar')->with('error', 'Bu kullanıcının siparişleri olduğu için silinemez!');
        }
        
        $kullanici->delete();
        
        return redirect()->route('admin.kullanicilar')->with('success', 'Kullanıcı başarıyla silindi!');
    }

    public function kategoriler()
    {
        $kategoriler = Kategori::with('urunler')->orderBy('created_at', 'desc')->get();
        return view('admin.kategoriler', compact('kategoriler'));
    }

    public function urunler()
    {
        $urunler = Urun::with(['magaza', 'kategori', 'urunGorselleri'])->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.urunler', compact('urunler'));
    }

    public function siparisler()
    {
        $siparisler = Siparis::with(['kullanici', 'siparisUrunleri'])->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.siparisler', compact('siparisler'));
    }

    public function magazalar()
    {
        $magazalar = Magaza::with(['kullanici', 'urunler.siparisUrunleri'])->orderBy('created_at', 'desc')->get();
        return view('admin.magazalar', compact('magazalar'));
    }

    public function magazaGoruntule($id)
    {
        $magaza = Magaza::with(['kullanici', 'urunler.kategori', 'urunler.urunGorselleri'])->findOrFail($id);
        return view('admin.magaza_detay', compact('magaza'));
    }

    public function magazaDuzenle($id)
    {
        $magaza = Magaza::with('kullanici')->findOrFail($id);
        return view('admin.magaza_duzenle', compact('magaza'));
    }

    public function magazaDuzenlePost(Request $request, $id)
    {
        $magaza = Magaza::findOrFail($id);
        
        $request->validate([
            'ad' => 'required|string|max:255',
            'aciklama' => 'nullable|string|max:1000',
        ]);

        $magaza->update([
            'ad' => $request->ad,
            'aciklama' => $request->aciklama,
        ]);

        return redirect()->route('admin.magazalar')->with('success', 'Mağaza başarıyla güncellendi!');
    }

    public function magazaSil($id)
    {
        $magaza = Magaza::findOrFail($id);
        
        // Mağazada ürün varsa silinemez
        if ($magaza->urunler()->count() > 0) {
            return redirect()->route('admin.magazalar')->with('error', 'Bu mağazada ürün bulunduğu için silinemez!');
        }
        
        $magaza->delete();
        
        return redirect()->route('admin.magazalar')->with('success', 'Mağaza başarıyla silindi!');
    }

    // Paythor API Yönetimi
    public function paythorApi()
    {
        // Admin kullanıcısının Paythor token'ını veritabanından al
        $admin = Auth::user();
        $paythorToken = $admin->paythor_token;
        
        // Session'dan da al (geriye uyumluluk için)
        if (!$paythorToken) {
            $paythorToken = session('paythor_token');
        }
        
        $paythorEmail = session('paythor_email');
        
        // OTP bekleme durumu için ekstra bilgiler
        $pendingEmail = session('pending_paythor_email');
        
        return view('admin.paythor_api', compact('paythorToken', 'paythorEmail', 'pendingEmail'));
    }

    public function paythorApiKaydet(Request $request)
    {
        $request->validate([
            'paythor_email' => 'required|email',
            'paythor_password' => 'required|min:6',
        ]);

        $email = $request->paythor_email;
        $password = $request->paythor_password;

        // PHP projenizdeki gibi aynı parametreler
        $program_id = 1;
        $app_id = 102;
        $store_url = config('app.url', 'https://pazaryeri.com');

        // İlk adım: Email/şifre ile giriş (PHP projesindeki gibi)
        $payload = [
            'auth_query' => [
                'auth_method' => 'email_password_panel',
                'email' => $email,
                'password' => $password,
                'program_id' => $program_id,
                'app_id' => $app_id,
                'store_url' => $store_url
            ]
        ];

        $ch = curl_init('https://dev-api.paythor.com/auth/signin');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        // Debug için log
        \Log::info('Paythor Login Response: ', $data);

        if ($httpCode === 200 && isset($data['data']['token_string'])) {
            $token = $data['data']['token_string'];
            
            // Eğer status "validation" ise OTP gerekebilir
            if (isset($data['data']['status']) && $data['data']['status'] === 'validation') {
                // OTP için gerekli bilgileri session'a kaydet
                session([
                    'pending_paythor_email' => $email,
                    'pending_paythor_password' => $password,
                    'pending_paythor_token' => $token
                ]);
                
                return redirect()->route('admin.paythor.api')->with('info', 'OTP kodu gerekli. E-posta adresinizi kontrol edin.');
            } else {
                // Direkt giriş başarılı - Admin kullanıcısına token'ı kaydet
                $admin = Auth::user();
                $admin->paythor_token = $token;
                $admin->save();
                
                session([
                    'paythor_token' => $token,
                    'paythor_email' => $email
                ]);

                return redirect()->route('admin.paythor.api')->with('success', 'Paythor API başarıyla bağlandı!');
            }
        }

        $errorMessage = $data['message'] ?? 'API bağlantı hatası.';
        return redirect()->route('admin.paythor.api')->with('error', $errorMessage);
    }

    public function paythorOtpDogrula(Request $request)
    {
        $request->validate([
            'otp' => 'required|min:4|max:8',
        ]);

        $email = session('pending_paythor_email');
        $token = session('pending_paythor_token');

        if (!$email || !$token) {
            return redirect()->route('admin.paythor.api')->with('error', 'OTP doğrulama oturumu sona erdi. Lütfen tekrar giriş yapın.');
        }

        // OTP doğrulama
        $otpPayload = [
            'target' => $email,
            'otp' => $request->otp
        ];

        $ch = curl_init('https://dev-api.paythor.com/otp/verify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($otpPayload));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($httpCode === 200 && isset($data['status']) && $data['status'] === 'success') {
            // OTP başarılı - Admin kullanıcısına token'ı kaydet
            $admin = Auth::user();
            $admin->paythor_token = $token;
            $admin->save();
            
            session([
                'paythor_token' => $token,
                'paythor_email' => $email
            ]);

            // Geçici session'ları temizle
            session()->forget(['pending_paythor_email', 'pending_paythor_password', 'pending_paythor_token']);

            return redirect()->route('admin.paythor.api')->with('success', 'Paythor API başarıyla bağlandı!');
        }

        $errorMessage = $data['message'] ?? 'OTP doğrulama başarısız.';
        return redirect()->route('admin.paythor.api')->with('error', $errorMessage);
    }

    public function paythorApiSil()
    {
        // Admin kullanıcısının token'ını sil
        $admin = Auth::user();
        $admin->paythor_token = null;
        $admin->save();
        
        // Tüm Paythor session verilerini temizle
        session()->forget([
            'paythor_token', 
            'paythor_email',
            'pending_paythor_email',
            'pending_paythor_password', 
            'pending_paythor_token'
        ]);
        
        \Log::info('Paythor API session data cleared');
        
        return redirect()->route('admin.paythor.api')->with('success', 'Paythor API bağlantısı kaldırıldı.');
    }

    public function urunGoruntule($id)
    {
        $urun = Urun::with(['magaza', 'kategori', 'urunGorselleri'])->findOrFail($id);
        return view('admin.urun_detay', compact('urun'));
    }

    public function urunDuzenle($id)
    {
        $urun = Urun::with(['magaza', 'kategori'])->findOrFail($id);
        $kategoriler = Kategori::all();
        $magazalar = Magaza::all();
        return view('admin.urun_duzenle', compact('urun', 'kategoriler', 'magazalar'));
    }

    public function urunDuzenlePost(Request $request, $id)
    {
        $urun = Urun::findOrFail($id);
        
        $request->validate([
            'ad' => 'required|string|max:255',
            'aciklama' => 'nullable|string|max:1000',
            'fiyat' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'kategori_id' => 'required|exists:kategoris,id',
            'durum' => 'required|in:aktif,pasif',
        ]);

        $urun->update([
            'ad' => $request->ad,
            'aciklama' => $request->aciklama,
            'fiyat' => $request->fiyat,
            'stok' => $request->stok,
            'kategori_id' => $request->kategori_id,
            'durum' => $request->durum,
        ]);

        return redirect()->route('admin.urunler')->with('success', 'Ürün başarıyla güncellendi!');
    }

    public function urunSil($id)
    {
        $urun = Urun::findOrFail($id);
        
        // Siparişte olan ürün silinemez
        if ($urun->siparisUrunleri()->count() > 0) {
            return redirect()->route('admin.urunler')->with('error', 'Bu ürün siparişlerde bulunduğu için silinemez!');
        }
        
        // Ürün görsellerini de sil
        $urun->urunGorselleri()->delete();
        
        $urun->delete();
        
        return redirect()->route('admin.urunler')->with('success', 'Ürün başarıyla silindi!');
    }
}
