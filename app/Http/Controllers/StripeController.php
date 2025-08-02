<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Siparis;
use App\Models\SiparisUrunu;
use App\Models\Urun;
use App\Models\Sepet;

class StripeController extends Controller
{
    public function __construct()
    {
        // Stripe API key'ini ayarla
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        } catch (\Exception $e) {
            Log::error('Stripe API key error: ' . $e->getMessage());
        }
    }

    /**
     * Stripe ödeme sayfası (adres bilgileri)
     */
    public function checkout(Request $request)
    {
        // Login kontrolü
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Ödeme yapabilmek için giriş yapmalısınız.');
        }

        try {
            // Debug: Sepet verilerini logla
            Log::info('Stripe checkout started for user: ' . Auth::id());
            
            // Sepet kontrolü - SepetController ile aynı mantıkla
            $sepetDetay = $this->getSepetData();
            
            Log::info('Sepet data:', ['items_count' => count($sepetDetay['items']), 'total' => $sepetDetay['toplam']]);
            
            if (empty($sepetDetay['items'])) {
                Log::warning('Empty cart for user: ' . Auth::id());
                return redirect()->route('sepet.index')->with('error', 'Sepetinizde geçerli ürün bulunmuyor.');
            }

            // Adres bilgileri sayfasına yönlendir
            return view('stripe.address', [
                'sepetUranlari' => $sepetDetay['items'],
                'toplam' => $sepetDetay['toplam']
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe checkout error: ' . $e->getMessage());
            return redirect()->route('sepet.index')
                           ->with('error', 'Ödeme sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Adres bilgilerini kaydet ve kart sayfasına yönlendir
     */
    public function saveAddress(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Giriş yapmanız gerekiyor.');
        }

        // Validasyon
        $request->validate([
            'ad_soyad' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefon' => 'required|string|max:20',
            'adres' => 'required|string|max:500',
            'il' => 'required|string|max:100',
            'ilce' => 'required|string|max:100',
            'posta_kodu' => 'nullable|string|max:10',
            'aciklama' => 'nullable|string|max:255',
        ], [
            'ad_soyad.required' => 'Ad soyad zorunludur',
            'email.required' => 'E-posta zorunludur',
            'email.email' => 'Geçerli bir e-posta adresi girin',
            'telefon.required' => 'Telefon zorunludur',
            'adres.required' => 'Adres zorunludur',
            'il.required' => 'İl zorunludur',
            'ilce.required' => 'İlçe zorunludur',
        ]);

        try {
            // Sepet kontrolü
            $sepetDetay = $this->getSepetData();
            if (empty($sepetDetay['items'])) {
                return redirect()->route('sepet.index')->with('error', 'Sepetinizde ürün bulunmuyor.');
            }

            // Adres bilgilerini session'a kaydet
            $addressData = $request->only(['ad_soyad', 'email', 'telefon', 'adres', 'il', 'ilce', 'posta_kodu', 'aciklama']);
            Session::put('stripe_address_data', $addressData);

            // Kart bilgileri sayfasına yönlendir
            return view('stripe.payment', [
                'sepetUranlari' => $sepetDetay['items'],
                'toplam' => $sepetDetay['toplam'],
                'addressData' => $addressData
            ]);

        } catch (\Exception $e) {
            Log::error('Address save error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Adres kaydedilirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Stripe başarılı ödeme
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        $orderId = $request->get('order_id');
        
        // Order ID ile geliyorsa (yeni sistem)
        if ($orderId) {
            $siparis = Siparis::find($orderId);
            if ($siparis && $siparis->kullanici_id == Auth::id()) {
                return view('stripe.success', compact('siparis'))
                      ->with('success', 'Ödemeniz başarıyla tamamlandı!');
            } else {
                return redirect()->route('sepet.index')->with('error', 'Sipariş bulunamadı.');
            }
        }
        
        // Session ID ile geliyorsa (eski sistem)
        if (!$sessionId) {
            return redirect()->route('sepet.index')->with('error', 'Geçersiz ödeme oturumu.');
        }

        try {
            // Stripe session'ını doğrula
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            
            if ($session->payment_status !== 'paid') {
                return redirect()->route('sepet.index')->with('error', 'Ödeme tamamlanmamış.');
            }

            // Sipariş oluştur
            $siparis = $this->createOrderFromSession($session);

            if ($siparis) {
                // Sepeti temizle
                $this->clearCart();
                
                return view('stripe.success', compact('siparis'))
                      ->with('success', 'Ödemeniz başarıyla tamamlandı!');
            } else {
                return redirect()->route('sepet.index')->with('error', 'Sipariş oluşturulurken hata oluştu.');
            }

        } catch (\Exception $e) {
            Log::error('Stripe success error: ' . $e->getMessage());
            return redirect()->route('sepet.index')
                           ->with('error', 'Ödeme doğrulanırken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Stripe iptal
     */
    public function cancel()
    {
        return redirect()->route('sepet.index')
                        ->with('warning', 'Ödeme işlemi iptal edildi.');
    }

    /**
     * Stripe webhook
     */
    public function webhook(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $sig_header = $request->server('HTTP_STRIPE_SIGNATURE');
        $endpoint_secret = config('services.stripe.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            // Webhook olaylarını işle
            switch ($event['type']) {
                case 'checkout.session.completed':
                    $session = $event['data']['object'];
                    $this->handleSuccessfulPayment($session);
                    break;
                
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event['data']['object'];
                    $this->handleFailedPayment($paymentIntent);
                    break;

                default:
                    Log::info('Unhandled Stripe webhook event: ' . $event['type']);
            }

            return response('Webhook handled', 200);

        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response('Webhook error', 400);
        }
    }

    /**
     * Sepet verilerini al (SepetController ile aynı mantık)
     */
    private function getSepetData()
    {
        $sepetUranlari = [];
        $toplam = 0;

        if (auth()->check()) {
            // Giriş yapmış kullanıcı için veritabanından sepeti al
            $sepetItems = Sepet::with(['urun.magaza'])
                ->where('kullanici_id', auth()->id())
                ->get();

            Log::info('Database cart items count: ' . $sepetItems->count());

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
            Log::info('Session cart:', $sepet);
            
            foreach ($sepet as $urunId => $miktar) {
                $urun = Urun::with('magaza')->find($urunId);
                if ($urun && $miktar > 0) {
                    $sepetUranlari[] = [
                        'urun' => $urun,
                        'miktar' => $miktar,
                        'ara_toplam' => $urun->fiyat * $miktar
                    ];
                    $toplam += $urun->fiyat * $miktar;
                }
            }
        }

        return ['items' => $sepetUranlari, 'toplam' => $toplam];
    }

    /**
     * Session'dan sipariş oluştur
     */
    private function createOrderFromSession($session)
    {
        try {
            $userId = $session->metadata->user_id;
            $customerData = json_decode($session->metadata->customer_data, true);

            // Sipariş oluştur
            $siparis = Siparis::create([
                'kullanici_id' => $userId,
                'toplam_tutar' => $session->amount_total / 100, // Stripe kuruştan TL'ye çevir
                'durum' => 'onaylandi',
                'teslimat_adresi' => ($customerData['adres'] ?? '') . ', ' . ($customerData['ilce'] ?? '') . '/' . ($customerData['il'] ?? ''),
                'teslimat_telefonu' => $customerData['telefon'] ?? '',
                'fatura_bilgileri' => $customerData,
                'payment_reference' => $session->payment_intent,
                'payment_status' => 'completed',
            ]);

            // Sepet ürünlerini sipariş ürünleri olarak kaydet
            $sepetDetay = $this->getSepetData();
            foreach ($sepetDetay['items'] as $item) {
                SiparisUrunu::create([
                    'siparis_id' => $siparis->id,
                    'urun_id' => $item['urun']->id,
                    'adet' => $item['miktar'],
                    'birim_fiyat' => $item['urun']->fiyat,
                ]);

                // Stoktan düş
                $item['urun']->decrement('stok', $item['miktar']);
            }

            return $siparis;

        } catch (\Exception $e) {
            Log::error('Order creation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sepeti temizle
     */
    private function clearCart()
    {
        if (auth()->check()) {
            // Veritabanından sepeti temizle
            Sepet::where('kullanici_id', auth()->id())->delete();
        } else {
            // Session'dan sepeti temizle
            Session::forget('sepet');
        }
        
        Session::forget(['stripe_session_id', 'stripe_customer_data']);
    }

    /**
     * Başarılı ödeme işlemi
     */
    private function handleSuccessfulPayment($session)
    {
        Log::info('Payment successful for session: ' . $session['id']);
    }

    /**
     * Başarısız ödeme işlemi
     */
    private function handleFailedPayment($paymentIntent)
    {
        Log::warning('Payment failed for intent: ' . $paymentIntent['id']);
    }

    /**
     * Payment Intent oluştur
     */
    public function createPaymentIntent(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Giriş yapmanız gerekiyor'], 401);
        }

        try {
            $amount = $request->input('amount'); // Kuruş cinsinden
            
            // Session'dan adres bilgilerini al
            $customerData = Session::get('stripe_address_data');
            if (!$customerData) {
                return response()->json(['message' => 'Adres bilgileri bulunamadı'], 400);
            }

            // Sepet kontrolü
            $sepetDetay = $this->getSepetData();
            if (empty($sepetDetay['items'])) {
                return response()->json(['message' => 'Sepetinizde ürün bulunmuyor'], 400);
            }

            // Tutarı doğrula
            $expectedAmount = $sepetDetay['toplam'] * 100;
            if ($amount != $expectedAmount) {
                return response()->json(['message' => 'Tutar uyuşmuyor'], 400);
            }

            // Payment Intent oluştur
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'try',
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'user_id' => Auth::id(),
                    'customer_data' => json_encode($customerData),
                ],
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Intent error: ' . $e->getMessage());
            return response()->json(['message' => 'Ödeme hazırlanırken hata oluştu'], 500);
        }
    }

    /**
     * Sipariş oluştur
     */
    public function createOrder(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Giriş yapmanız gerekiyor'], 401);
        }

        try {
            $paymentIntentId = $request->input('payment_intent_id');
            $customerData = $request->input('customer_data');

            // Payment Intent'i doğrula
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            
            if ($paymentIntent->status !== 'succeeded') {
                return response()->json(['message' => 'Ödeme tamamlanmamış'], 400);
            }

            if ($paymentIntent->metadata->user_id != Auth::id()) {
                return response()->json(['message' => 'Yetkisiz işlem'], 403);
            }

            // Sipariş oluştur
            $sepetDetay = $this->getSepetData();
            if (empty($sepetDetay['items'])) {
                return response()->json(['message' => 'Sepet boş'], 400);
            }

            // Teslimat adresini oluştur (uzunluk sınırı ile)
            $teslimatAdresi = ($customerData['adres'] ?? '') . ', ' . ($customerData['ilce'] ?? '') . '/' . ($customerData['il'] ?? '');
            $teslimatAdresi = substr($teslimatAdresi, 0, 500); // Maksimum 500 karakter

            $siparis = Siparis::create([
                'kullanici_id' => Auth::id(),
                'toplam_tutar' => $paymentIntent->amount / 100,
                'durum' => 'onaylandi',
                'teslimat_adresi' => $teslimatAdresi,
                'teslimat_telefonu' => substr($customerData['telefon'] ?? '', 0, 20),
                'fatura_bilgileri' => $customerData,
                'payment_reference' => $paymentIntentId,
                'payment_status' => 'paid', // 'completed' yerine 'paid' kullan
            ]);

            // Sipariş ürünlerini kaydet
            foreach ($sepetDetay['items'] as $item) {
                SiparisUrunu::create([
                    'siparis_id' => $siparis->id,
                    'urun_id' => $item['urun']->id,
                    'adet' => $item['miktar'],
                    'birim_fiyat' => $item['urun']->fiyat,
                ]);

                // Stoktan düş
                $item['urun']->decrement('stok', $item['miktar']);
            }

            // Sepeti temizle
            $this->clearCart();

            return response()->json([
                'order_id' => $siparis->id,
                'message' => 'Sipariş başarıyla oluşturuldu'
            ]);

        } catch (\Exception $e) {
            Log::error('Order creation error: ' . $e->getMessage());
            return response()->json(['message' => 'Sipariş oluşturulurken hata oluştu'], 500);
        }
    }
}