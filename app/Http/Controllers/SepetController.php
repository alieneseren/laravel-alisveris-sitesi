<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\Urun;
use App\Models\Kullanici;
use App\Models\Siparis;
use App\Models\SiparisUrunu;
use App\Models\Sepet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SiparisOnayMail;



class SepetController extends Controller
{
    /**
     * Admin kullanıcısının Paythor token'ını alır
     */
    private function getAdminPaythorToken()
    {
        // Admin kullanıcısını bul (rol = 'yonetici')
        $admin = \App\Models\Kullanici::where('rol', 'yonetici')->first();
        if ($admin && $admin->paythor_token) {
            return $admin->paythor_token;
        }
        // Geriye uyumluluk için session'ı da kontrol et
        return session('paythor_token');
    }

    /**
     * Sipariş oluştur
     */
    private function createOrder($sepetUranlari, $toplamTutar, $adresData = [])
    {
        try {
            DB::beginTransaction();

            // Sipariş oluştur
            $siparis = Siparis::create([
                'kullanici_id' => auth()->id(),
                'toplam_tutar' => $toplamTutar,
                'durum' => 'beklemede',
                'teslimat_adresi' => $adresData['teslimat_adresi'] ?? null,
                'teslimat_telefonu' => $adresData['teslimat_telefonu'] ?? null,
                'fatura_bilgileri' => $adresData['fatura_bilgileri'] ?? [],
                'payment_reference' => $adresData['payment_reference'] ?? null,
                'payment_status' => $adresData['payment_status'] ?? 'pending'
            ]);

            // Sipariş ürünlerini oluştur
            foreach ($sepetUranlari as $item) {
                SiparisUrunu::create([
                    'siparis_id' => $siparis->id,
                    'urun_id' => $item['urun']->id,
                    'adet' => $item['miktar'],
                    'birim_fiyat' => $item['urun']->fiyat
                ]);
            }

            DB::commit();
            return $siparis;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Sipariş oluşturma hatası: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Stokları azalt
     */
    private function decreaseStock($siparis)
    {
        try {
            foreach ($siparis->siparisUrunleri as $siparisUrunu) {
                $urun = $siparisUrunu->urun;
                if ($urun && $urun->stok >= $siparisUrunu->adet) {
                    $urun->stok -= $siparisUrunu->adet;
                    $urun->save();
                    \Log::info('Stok azaltıldı', [
                        'urun_id' => $urun->id, 
                        'azalan_miktar' => $siparisUrunu->adet, 
                        'kalan_stok' => $urun->stok
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Stok azaltma hatası: ' . $e->getMessage());
        }
    }

    /**
     * Sipariş onay maili gönder
     */
    private function sendOrderConfirmationEmail($siparis)
    {
        try {
            $faturaBilgileri = json_decode($siparis->fatura_bilgileri, true);
            $email = $faturaBilgileri['email'] ?? null;
            
            if ($email) {
                Mail::to($email)->send(new SiparisOnayMail($siparis));
                \Log::info('Sipariş onay maili gönderildi', [
                    'siparis_id' => $siparis->id, 
                    'email' => $email
                ]);
            } else {
                \Log::warning('Email adresi bulunamadı', ['siparis_id' => $siparis->id]);
            }
        } catch (\Exception $e) {
            \Log::error('Mail gönderme hatası: ' . $e->getMessage(), [
                'siparis_id' => $siparis->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function index()
    {
        $sepetUranlari = [];
        $toplam = 0;

        if (auth()->check()) {
            // Giriş yapmış kullanıcı için veritabanından sepeti al
            $sepetItems = Sepet::with(['urun.magaza'])
                ->where('kullanici_id', auth()->id())
                ->get();

            foreach ($sepetItems as $item) {
                if ($item->urun) {
                    $sepetUranlari[] = [
                        'urun' => $item->urun,
                        'miktar' => $item->miktar,
                        'ara_toplam' => $item->urun->fiyat * $item->miktar
                    ];
                    $toplam += $item->urun->fiyat * $item->miktar;
                }
            }
        } else {
            // Misafir kullanıcı için session'dan sepeti al
            $sepet = Session::get('sepet', []);
            foreach ($sepet as $urunId => $miktar) {
                $urun = Urun::with('magaza')->find($urunId);
                if ($urun) {
                    $sepetUranlari[] = [
                        'urun' => $urun,
                        'miktar' => $miktar,
                        'ara_toplam' => $urun->fiyat * $miktar
                    ];
                    $toplam += $urun->fiyat * $miktar;
                }
            }
        }

        // Ödeme sistemi durumu kontrolü
        $odemeAktif = !empty($this->getAdminPaythorToken());

        // Paythor ödeme durumu kontrolü (PHP projesindeki gibi)
        if (request()->has('success') && request()->get('success') == '1') {
            // Ödeme başarılı - sepeti temizle
            Session::forget('sepet');
            return redirect()->route('sepet')->with('success', 'Ödemeniz başarıyla tamamlandı! Siparişiniz işleme alınmıştır.');
        }

        if (request()->has('cancel') && request()->get('cancel') == '1') {
            return redirect()->route('sepet')->with('warning', 'Ödeme işlemi iptal edildi. Sepetiniz korundu.');
        }

        return view('sepet.index', compact('sepetUranlari', 'toplam', 'odemeAktif'));
    }

    public function ekle(Request $request)
    {
        try {
            $urunId = $request->urun_id;
            $miktar = $request->miktar ?? 1;

            if (!$urunId) {
                return response()->json(['success' => false, 'message' => 'Ürün ID gerekli']);
            }

            $urun = Urun::find($urunId);
            if (!$urun) {
                return response()->json(['success' => false, 'message' => 'Ürün bulunamadı']);
            }

            if (auth()->check()) {
                // Giriş yapmış kullanıcı için veritabanı işlemi
                $kullaniciId = auth()->id();
                $sepetItem = Sepet::where('kullanici_id', $kullaniciId)
                    ->where('urun_id', $urunId)
                    ->first();

                $sepettekiMiktar = $sepetItem ? $sepetItem->miktar : 0;
                $toplamMiktar = $sepettekiMiktar + $miktar;

                if ($toplamMiktar > $urun->stok) {
                    $kalanStok = $urun->stok - $sepettekiMiktar;
                    if ($kalanStok <= 0) {
                        return response()->json(['success' => false, 'message' => 'Bu ürün sepetinizde zaten maksimum miktarda bulunuyor']);
                    } else {
                        return response()->json(['success' => false, 'message' => "En fazla {$kalanStok} adet daha ekleyebilirsiniz (Stok: {$urun->stok}, Sepetinizde: {$sepettekiMiktar})"]);
                    }
                }

                if ($sepetItem) {
                    $sepetItem->miktar += $miktar;
                    $sepetItem->save();
                } else {
                    Sepet::create([
                        'kullanici_id' => $kullaniciId,
                        'urun_id' => $urunId,
                        'miktar' => $miktar
                    ]);
                }

                // Toplam sepet sayısını hesapla
                $toplamSepetSayisi = Sepet::where('kullanici_id', $kullaniciId)->sum('miktar');

            } else {
                // Misafir kullanıcı için session işlemi
                $sepet = Session::get('sepet', []);
                
                // Sepetteki mevcut miktarı kontrol et
                $sepettekiMiktar = isset($sepet[$urunId]) ? $sepet[$urunId] : 0;
                $toplamMiktar = $sepettekiMiktar + $miktar;

                if ($toplamMiktar > $urun->stok) {
                    $kalanStok = $urun->stok - $sepettekiMiktar;
                    if ($kalanStok <= 0) {
                        return response()->json(['success' => false, 'message' => 'Bu ürün sepetinizde zaten maksimum miktarda bulunuyor']);
                    } else {
                        return response()->json(['success' => false, 'message' => "En fazla {$kalanStok} adet daha ekleyebilirsiniz (Stok: {$urun->stok}, Sepetinizde: {$sepettekiMiktar})"]);
                    }
                }
                
                if (isset($sepet[$urunId])) {
                    $sepet[$urunId] += $miktar;
                } else {
                    $sepet[$urunId] = $miktar;
                }

                Session::put('sepet', $sepet);
                $toplamSepetSayisi = array_sum($sepet);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Ürün sepete eklendi',
                'sepet_sayisi' => $toplamSepetSayisi,
                'odeme_aktif' => !empty($this->getAdminPaythorToken())
            ]);
        } catch (\Exception $e) {
            \Log::error('Sepet ekle hatası: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Sistem hatası oluştu']);
        }
    }

    public function cikar($id)
    {
        if (auth()->check()) {
            // Giriş yapmış kullanıcı için veritabanından sil
            Sepet::where('kullanici_id', auth()->id())
                ->where('urun_id', $id)
                ->delete();
        } else {
            // Misafir kullanıcı için session'dan sil
            $sepet = Session::get('sepet', []);
            if (isset($sepet[$id])) {
                unset($sepet[$id]);
                Session::put('sepet', $sepet);
            }
        }

        return redirect()->route('sepet')->with('success', 'Ürün sepetten çıkarıldı');
    }

    public function count()
    {
        if (auth()->check()) {
            // Giriş yapmış kullanıcı için veritabanından say
            $count = Sepet::where('kullanici_id', auth()->id())->sum('miktar');
        } else {
            // Misafir kullanıcı için session'dan say
            $sepet = Session::get('sepet', []);
            $count = array_sum($sepet);
        }

        return response()->json([
            'count' => $count,
            'odeme_aktif' => !empty($this->getAdminPaythorToken())
        ]);
    }

    public function adetGuncelle(Request $request)
    {
        $urunId = $request->urun_id;
        $adet = intval($request->adet);

        if ($adet < 0) {
            return response()->json(['success' => false, 'message' => 'Geçersiz adet']);
        }

        // Ürün bilgilerini al
        $urun = Urun::find($urunId);
        if (!$urun) {
            return response()->json(['success' => false, 'message' => 'Ürün bulunamadı']);
        }

        // Stok kontrolü
        if ($adet > $urun->stok) {
            return response()->json(['success' => false, 'message' => "En fazla {$urun->stok} adet ekleyebilirsiniz"]);
        }

        if (auth()->check()) {
            // Giriş yapmış kullanıcı için veritabanı işlemi
            $kullaniciId = auth()->id();
            
            if ($adet == 0) {
                // Ürünü sepetten çıkar
                Sepet::where('kullanici_id', $kullaniciId)
                    ->where('urun_id', $urunId)
                    ->delete();
            } else {
                // Adeti güncelle
                Sepet::updateOrCreate(
                    ['kullanici_id' => $kullaniciId, 'urun_id' => $urunId],
                    ['miktar' => $adet]
                );
            }

            // Toplam hesapla
            $sepetItems = Sepet::with('urun')->where('kullanici_id', $kullaniciId)->get();
            $toplamGenel = 0;
            foreach ($sepetItems as $item) {
                if ($item->urun) {
                    $toplamGenel += $item->urun->fiyat * $item->miktar;
                }
            }
        } else {
            // Misafir kullanıcı için session işlemi
            $sepet = Session::get('sepet', []);
            
            if ($adet == 0) {
                // Ürünü sepetten çıkar
                unset($sepet[$urunId]);
            } else {
                // Adeti güncelle
                $sepet[$urunId] = $adet;
            }

            Session::put('sepet', $sepet);

            // Toplam hesapla
            $toplamGenel = 0;
            foreach ($sepet as $id => $miktar) {
                $u = Urun::find($id);
                if ($u) {
                    $toplamGenel += $u->fiyat * $miktar;
                }
            }
        }

        // Güncel ara toplam hesapla
        $araToplam = $urun ? $urun->fiyat * $adet : 0;

        return response()->json([
            'success' => true,
            'adet' => $adet,
            'ara_toplam' => number_format($araToplam, 2),
            'toplam' => number_format($toplamGenel, 2)
        ]);
    }

    public function odeme()
    {
        // Giriş yapmamış kullanıcıları login sayfasına yönlendir
        if (!auth()->check()) {
            return redirect()->route('login')->with('info', 'Ödeme işlemi için giriş yapmanız gerekiyor. Sepetiniz korunacak.');
        }

        // Admin kullanıcısının Paythor token'ını al
        $paythorToken = $this->getAdminPaythorToken();
        if (!$paythorToken) {
            return redirect()->route('sepet')->with('error', 'Ödeme sistemi henüz aktif değil. Lütfen yöneticiyle iletişime geçin.');
        }

        // Giriş yapmış kullanıcı için veritabanından sepeti al
        $sepetItems = Sepet::with(['urun.magaza'])
            ->where('kullanici_id', auth()->id())
            ->get();

        if ($sepetItems->isEmpty()) {
            return redirect()->route('sepet')->with('error', 'Sepetiniz boş.');
        }

        $sepetUranlari = [];
        $toplam = 0;

        foreach ($sepetItems as $item) {
            if ($item->urun) {
                $sepetUranlari[] = [
                    'urun' => $item->urun,
                    'miktar' => $item->miktar,
                    'ara_toplam' => $item->urun->fiyat * $item->miktar
                ];
                $toplam += $item->urun->fiyat * $item->miktar;
            }
        }

        return view('sepet.odeme', compact('sepetUranlari', 'toplam', 'paythorToken'));
    }

    public function odemeYap(Request $request)
    {
        // Giriş yapmamış kullanıcıları login sayfasına yönlendir
        if (!auth()->check()) {
            return redirect()->route('login')->with('info', 'Ödeme işlemi için giriş yapmanız gerekiyor. Sepetiniz korunacak.');
        }

        // Admin kullanıcısının Paythor token'ını al
        $paythorToken = $this->getAdminPaythorToken();
        \Log::info('Current Admin Paythor Token', [
            'has_token' => !empty($paythorToken),
            'token_length' => $paythorToken ? strlen($paythorToken) : 0,
            'token_preview' => $paythorToken ? substr($paythorToken, 0, 20) . '...' : 'null'
        ]);
        
        if (!$paythorToken) {
            return redirect()->route('sepet')->with('error', 'Ödeme sistemi henüz aktif değil. Lütfen admin panelinden Paythor API\'sini yeniden bağlayın.');
        }

        // Giriş yapmış kullanıcı için veritabanından sepeti al
        $sepetItems = Sepet::with('urun')->where('kullanici_id', auth()->id())->get();
        
        if ($sepetItems->isEmpty()) {
            return redirect()->route('sepet')->with('error', 'Sepetiniz boş.');
        }

        // Sepet toplamını hesapla
        $toplam = 0;
        $sepetUranlari = [];
        
        foreach ($sepetItems as $item) {
            if ($item->urun) {
                $araToplam = $item->urun->fiyat * $item->miktar;
                $toplam += $araToplam;
                $sepetUranlari[] = [
                    'urun' => $item->urun,
                    'miktar' => $item->miktar,
                    'ara_toplam' => $araToplam
                ];
            }
        }

        // Sepet ürünlerini cart formatında hazırla
        $cart = [];
        foreach ($sepetUranlari as $item) {
            // Debug: Ürün bilgilerini kontrol et
            \Log::info('Product info for cart', [
                'urun_id' => $item['urun']->id,
                'urun_ad' => $item['urun']->ad,
                'urun_fiyat' => $item['urun']->fiyat,
                'miktar' => $item['miktar']
            ]);
            
            $cart[] = [
                'id' => 'PRD-' . $item['urun']->id,
                'name' => $item['urun']->ad ?? 'Ürün', // 'ad' alanını kullan, yoksa varsayılan
                'type' => 'product',
                'price' => number_format($item['urun']->fiyat, 2, '.', ''),
                'quantity' => $item['miktar']
            ];
        }

        // Önce siparişi oluştur
        $merchantReference = 'LARAVEL-' . time() . '-' . (auth()->id() ?? 'guest');
        
        try {
            // Müşteri adres bilgilerini hazırla
            $adSoyad = $request->ad_soyad ?? auth()->user()->name ?? 'Müşteri';
            $email = $request->email ?? auth()->user()->email ?? 'musteri@example.com';
            $telefon = $request->telefon ?? auth()->user()->telefon ?? '5000000000';
            $adres = $request->adres ?? 'Adres Bilgisi';
            $il = $request->il ?? 'İstanbul';
            $postaKodu = $request->posta_kodu ?? '34000';
            
            // Teslimat adresi string formatı
            $teslimatAdresi = "$adres, $il $postaKodu";
            
            // Fatura bilgileri JSON formatı
            $faturaBilgileri = [
                'ad_soyad' => $adSoyad,
                'email' => $email,
                'telefon' => $telefon,
                'adres' => $adres,
                'il' => $il,
                'posta_kodu' => $postaKodu
            ];
            
            // Siparişi oluştur
            $siparis = $this->createOrder($sepetUranlari, $toplam, [
                'teslimat_adresi' => $teslimatAdresi,
                'teslimat_telefonu' => $telefon,
                'fatura_bilgileri' => $faturaBilgileri,
                'payment_reference' => $merchantReference,
                'payment_status' => 'pending'
            ]);
            
            \Log::info('Order created before payment', [
                'siparis_id' => $siparis->id,
                'merchant_reference' => $merchantReference,
                'total_amount' => $toplam
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('sepet')->with('error', 'Sipariş oluşturulamadı: ' . $e->getMessage());
        }

        // Paythor API'nin tam formatı
        $odemeData = [
            'payment' => [
                'amount' => number_format($toplam, 2, '.', ''),
                'currency' => 'TRY',
                'buyer_fee' => '0',
                'method' => 'creditcard',
                'merchant_reference' => $merchantReference,
                'return_url' => route('odeme.basarili'),
                'cancel_url' => route('odeme.basarisiz'),
                'callback_url' => route('paythor.callback')
            ],
            'payer' => [
                'first_name' => explode(' ', $request->ad_soyad ?? auth()->user()->name ?? 'Müşteri')[0],
                'last_name' => explode(' ', $request->ad_soyad ?? auth()->user()->name ?? 'Müşteri')[1] ?? '',
                'email' => $request->email ?? auth()->user()->email ?? 'musteri@example.com',
                'phone' => $request->telefon ?? auth()->user()->telefon ?? '5000000000',
                'address' => [
                    'line_1' => $request->adres ?? 'Adres Bilgisi',
                    'city' => $request->il ?? 'İstanbul',
                    'state' => $request->il ?? 'İstanbul',
                    'postal_code' => $request->posta_kodu ?? '34000',
                    'country' => 'TR'
                ],
                'ip' => request()->ip() ?? '127.0.0.1'
            ],
            'order' => [
                'cart' => $cart,
                'shipping' => [
                    'first_name' => explode(' ', $request->ad_soyad ?? auth()->user()->name ?? 'Müşteri')[0],
                    'last_name' => explode(' ', $request->ad_soyad ?? auth()->user()->name ?? 'Müşteri')[1] ?? '',
                    'phone' => $request->telefon ?? auth()->user()->telefon ?? '5000000000',
                    'email' => $request->email ?? auth()->user()->email ?? 'musteri@example.com',
                    'address' => [
                        'line_1' => $request->adres ?? 'Adres Bilgisi',
                        'city' => $request->il ?? 'İstanbul',
                        'state' => $request->il ?? 'İstanbul',
                        'postal_code' => $request->posta_kodu ?? '34000',
                        'country' => 'TR'
                    ]
                ],
                'invoice' => [
                    'id' => 'cart_hash_' . md5(serialize($cart)),
                    'first_name' => explode(' ', $request->ad_soyad ?? auth()->user()->name ?? 'Müşteri')[0],
                    'last_name' => explode(' ', $request->ad_soyad ?? auth()->user()->name ?? 'Müşteri')[1] ?? '',
                    'price' => number_format($toplam, 2, '.', ''),
                    'quantity' => 1
                ]
            ]
        ];

        // cURL isteği - Paythor API'nin doğru endpoint'i
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://dev-api.paythor.com/payment/create');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $paythorToken
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($odemeData));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Debug log - Paythor API formatı
        \Log::info('Paythor Payment Request (Full Format)', [
            'endpoint' => 'https://dev-api.paythor.com/payment/create',
            'token_preview' => substr($paythorToken, 0, 20) . '...',
            'request_data' => $odemeData,
            'response_code' => $httpCode,
            'response_body' => $response,
            'curl_error' => $curlError
        ]);

        if ($curlError) {
            return redirect()->route('sepet')->with('error', 'Bağlantı hatası: ' . $curlError);
        }

        if ($httpCode == 200 || $httpCode == 201) {
            $responseData = json_decode($response, true);
            
            // Paythor API response formatlarını kontrol et
            $paymentLink = null;
            if (isset($responseData['data']['payment_url'])) {
                $paymentLink = $responseData['data']['payment_url'];
            } elseif (isset($responseData['payment_url'])) {
                $paymentLink = $responseData['payment_url'];
            } elseif (isset($responseData['data']['payment_link'])) {
                $paymentLink = $responseData['data']['payment_link'];
            } elseif (isset($responseData['payment_link'])) {
                $paymentLink = $responseData['payment_link'];
            } elseif (isset($responseData['url'])) {
                $paymentLink = $responseData['url'];
            } elseif (isset($responseData['links']) && is_array($responseData['links']) && count($responseData['links']) > 0) {
                $paymentLink = $responseData['links'][0]['href'] ?? null;
            }
            
            if ($paymentLink) {
                \Log::info('Payment Link Generated', ['payment_link' => $paymentLink]);
                return redirect($paymentLink);
            } else {
                \Log::warning('Payment Link Not Found in Response', ['response' => $responseData]);
                return redirect()->route('sepet')->with('error', 'Ödeme linki alınamadı. API yanıtı: ' . json_encode($responseData));
            }
        }

        // Hata durumu
        $errorData = json_decode($response, true);
        $errorMessage = 'Ödeme işlemi başarısız.';
        
        if (isset($errorData['message'])) {
            $errorMessage = $errorData['message'];
        } elseif (isset($errorData['error'])) {
            $errorMessage = $errorData['error'];
        } elseif (isset($errorData['errors'])) {
            $errorMessage = is_array($errorData['errors']) ? implode(', ', $errorData['errors']) : $errorData['errors'];
        }
        
        \Log::error('Paythor Payment Error', [
            'http_code' => $httpCode,
            'error_message' => $errorMessage,
            'full_response' => $response
        ]);

        return redirect()->route('sepet')->with('error', $errorMessage . ' (HTTP: ' . $httpCode . ')');
    }

    /**
     * PayThor webhook/callback handler
     */
    public function paythorCallback(Request $request)
    {
        \Log::info('PayThor Callback Received', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'raw_body' => $request->getContent()
        ]);

        try {
            $data = $request->all();
            
            // PayThor'dan gelen veriyi analiz et
            $merchantReference = $data['merchant_reference'] ?? null;
            $paymentStatus = $data['status'] ?? $data['payment_status'] ?? 'unknown';
            $transactionId = $data['transaction_id'] ?? $data['id'] ?? null;
            
            if (!$merchantReference) {
                \Log::error('PayThor Callback: merchant_reference not found', $data);
                return response('Merchant reference not found', 400);
            }
            
            // Siparişi bul
            $siparis = Siparis::where('payment_reference', $merchantReference)->first();
            
            if (!$siparis) {
                \Log::error('PayThor Callback: Order not found', [
                    'merchant_reference' => $merchantReference,
                    'callback_data' => $data
                ]);
                return response('Order not found', 404);
            }
            
            // Ödeme durumunu güncelle
            $oldStatus = $siparis->payment_status;
            
            switch (strtolower($paymentStatus)) {
                case 'success':
                case 'completed':
                case 'paid':
                    $siparis->payment_status = 'completed';
                    $siparis->durum = 'onaylandi';
                    
                    // Stok azalt
                    $this->decreaseStock($siparis);
                    
                    // Email gönder
                    $this->sendOrderConfirmationEmail($siparis);
                    break;
                    
                case 'failed':
                case 'cancelled':
                case 'error':
                    $siparis->payment_status = 'failed';
                    $siparis->durum = 'iptal';
                    break;
                    
                default:
                    $siparis->payment_status = 'pending';
                    break;
            }
            
            $siparis->save();
            
            \Log::info('PayThor Callback Processed', [
                'siparis_id' => $siparis->id,
                'old_status' => $oldStatus,
                'new_status' => $siparis->payment_status,
                'payment_status' => $paymentStatus,
                'transaction_id' => $transactionId
            ]);
            
            return response('OK', 200);
            
        } catch (\Exception $e) {
            \Log::error('PayThor Callback Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response('Callback processing failed', 500);
        }
    }
    
    /**
     * Ödeme başarılı sayfası
     */
    public function odemeBasarili(Request $request)
    {
        $merchantReference = $request->get('merchant_reference');
        $siparis = null;
        
        if ($merchantReference) {
            $siparis = Siparis::where('payment_reference', $merchantReference)->first();
        }
        
        // Sepeti temizle
        if (auth()->check()) {
            // Giriş yapmış kullanıcı için veritabanından sepeti temizle
            Sepet::where('kullanici_id', auth()->id())->delete();
        } else {
            // Misafir kullanıcı için session sepetini temizle
            session()->forget('sepet');
        }
        
        return view('odeme.basarili', compact('siparis'));
    }
    
    /**
     * Ödeme başarısız sayfası
     */
    public function odemeBasarisiz(Request $request)
    {
        $merchantReference = $request->get('merchant_reference');
        $siparis = null;
        
        if ($merchantReference) {
            $siparis = Siparis::where('payment_reference', $merchantReference)->first();
        }
        
        return view('odeme.basarisiz', compact('siparis'));
    }

    /**
     * Stripe checkout form sayfası
     */
    public function stripeCheckout()
    {
        // Login kontrolü
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Ödeme yapabilmek için giriş yapmalısınız.');
        }

        // Sepet verilerini al (index() metodundaki ile aynı mantık)
        $sepetUranlari = [];
        $toplam = 0;

        if (auth()->check()) {
            // Giriş yapmış kullanıcı için veritabanından sepeti al
            $sepetItems = Sepet::with(['urun.magaza'])
                ->where('kullanici_id', auth()->id())
                ->get();

            foreach ($sepetItems as $item) {
                if ($item->urun) {
                    $sepetUranlari[] = [
                        'urun' => $item->urun,
                        'miktar' => $item->miktar,
                        'ara_toplam' => $item->urun->fiyat * $item->miktar
                    ];
                    $toplam += $item->urun->fiyat * $item->miktar;
                }
            }
        } else {
            // Misafir kullanıcı için session'dan sepeti al
            $sepet = Session::get('sepet', []);
            foreach ($sepet as $urunId => $miktar) {
                $urun = Urun::with('magaza')->find($urunId);
                if ($urun) {
                    $sepetUranlari[] = [
                        'urun' => $urun,
                        'miktar' => $miktar,
                        'ara_toplam' => $urun->fiyat * $miktar
                    ];
                    $toplam += $urun->fiyat * $miktar;
                }
            }
        }

        // Sepet kontrolü
        if (empty($sepetUranlari)) {
            return redirect()->route('sepet.index')->with('error', 'Sepetinizde geçerli ürün bulunmuyor.');
        }

        // Adres sayfasına yönlendir
        return view('stripe.address', [
            'sepetUranlari' => $sepetUranlari,
            'toplam' => $toplam
        ]);
    }
}
