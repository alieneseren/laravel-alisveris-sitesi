# PayThor Ödeme Sistemi Entegrasyon Paketi

Bu dosya, Laravel projenizdeki PayThor ödeme sistemini başka bir projeye entegre etmeniz için gerekli tüm kodları içerir.

## 📋 İçindekiler

1. [Gerekli Dosyalar](#gerekli-dosyalar)
2. [Controller Kodları](#controller-kodları)
3. [Model Kodları](#model-kodları)
4. [Routes Tanımları](#routes-tanımları)
5. [Blade View Dosyaları](#blade-view-dosyaları)
6. [Migration Dosyaları](#migration-dosyaları)
7. [Kurulum Adımları](#kurulum-adımları)

---

## 🔧 Gerekli Dosyalar

### 1. PaymentController.php (Yeni Controller)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Order; // Kendi sipariş modeliniz
use App\Models\User;  // Kendi kullanıcı modeliniz

class PaymentController extends Controller
{
    /**
     * Admin kullanıcısının Paythor token'ını al
     */
    private function getPaythorToken()
    {
        // Admin kullanıcısından token al (admin tablosunda paythor_token alanı olmalı)
        $admin = User::where('role', 'admin')->first();
        
        if ($admin && $admin->paythor_token) {
            return $admin->paythor_token;
        }
        
        // .env dosyasından da alabilirsiniz
        return env('PAYTHOR_TOKEN');
    }

    /**
     * Ödeme işlemini başlat
     */
    public function processPayment(Request $request)
    {
        $paythorToken = $this->getPaythorToken();
        
        if (!$paythorToken) {
            return redirect()->back()->with('error', 'Ödeme sistemi aktif değil.');
        }

        // Sipariş bilgilerini al
        $orderId = $request->order_id;
        $amount = $request->amount;
        $customerData = $request->only(['name', 'email', 'phone', 'address', 'city']);
        
        // Unique merchant reference oluştur
        $merchantReference = 'ORDER-' . $orderId . '-' . time();
        
        // PayThor API için veri hazırla
        $paymentData = [
            'payment' => [
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'TRY',
                'buyer_fee' => '0',
                'method' => 'creditcard',
                'merchant_reference' => $merchantReference,
                'return_url' => route('payment.success'),
                'cancel_url' => route('payment.cancel'),
                'callback_url' => route('payment.callback')
            ],
            'payer' => [
                'first_name' => explode(' ', $customerData['name'])[0] ?? 'Müşteri',
                'last_name' => explode(' ', $customerData['name'])[1] ?? '',
                'email' => $customerData['email'],
                'phone' => $customerData['phone'],
                'address' => [
                    'line_1' => $customerData['address'],
                    'city' => $customerData['city'] ?? 'İstanbul',
                    'state' => $customerData['city'] ?? 'İstanbul',
                    'postal_code' => '34000',
                    'country' => 'TR'
                ],
                'ip' => request()->ip() ?? '127.0.0.1'
            ],
            'order' => [
                'cart' => [
                    [
                        'id' => 'ORDER-' . $orderId,
                        'name' => 'Sipariş #' . $orderId,
                        'type' => 'product',
                        'price' => number_format($amount, 2, '.', ''),
                        'quantity' => 1
                    ]
                ],
                'shipping' => [
                    'first_name' => explode(' ', $customerData['name'])[0] ?? 'Müşteri',
                    'last_name' => explode(' ', $customerData['name'])[1] ?? '',
                    'phone' => $customerData['phone'],
                    'email' => $customerData['email'],
                    'address' => [
                        'line_1' => $customerData['address'],
                        'city' => $customerData['city'] ?? 'İstanbul',
                        'state' => $customerData['city'] ?? 'İstanbul',
                        'postal_code' => '34000',
                        'country' => 'TR'
                    ]
                ],
                'invoice' => [
                    'id' => 'INV-' . $orderId,
                    'first_name' => explode(' ', $customerData['name'])[0] ?? 'Müşteri',
                    'last_name' => explode(' ', $customerData['name'])[1] ?? '',
                    'price' => number_format($amount, 2, '.', ''),
                    'quantity' => 1
                ]
            ]
        ];

        // Siparişi güncelle (payment_reference ekle)
        Order::where('id', $orderId)->update([
            'payment_reference' => $merchantReference,
            'payment_status' => 'pending'
        ]);

        // PayThor API'ye istek gönder
        $response = $this->sendPaythorRequest($paymentData, $paythorToken);
        
        if ($response['success']) {
            return redirect($response['payment_url']);
        } else {
            return redirect()->back()->with('error', $response['error']);
        }
    }

    /**
     * PayThor API'ye istek gönder
     */
    private function sendPaythorRequest($data, $token)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://dev-api.paythor.com/payment/create');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        Log::info('PayThor API Request', [
            'data' => $data,
            'response' => $response,
            'http_code' => $httpCode,
            'curl_error' => $curlError
        ]);

        if ($curlError) {
            return ['success' => false, 'error' => 'Bağlantı hatası: ' . $curlError];
        }

        if ($httpCode == 200 || $httpCode == 201) {
            $responseData = json_decode($response, true);
            
            // Payment URL'ini bul
            $paymentUrl = $responseData['data']['payment_url'] ?? 
                         $responseData['payment_url'] ?? 
                         $responseData['url'] ?? null;
            
            if ($paymentUrl) {
                return ['success' => true, 'payment_url' => $paymentUrl];
            }
        }

        $errorData = json_decode($response, true);
        $errorMessage = $errorData['message'] ?? $errorData['error'] ?? 'Ödeme hatası';
        
        return ['success' => false, 'error' => $errorMessage];
    }

    /**
     * PayThor callback handler
     */
    public function callback(Request $request)
    {
        Log::info('PayThor Callback', $request->all());

        try {
            $merchantReference = $request->merchant_reference;
            $paymentStatus = $request->status ?? $request->payment_status ?? 'unknown';
            
            if (!$merchantReference) {
                return response('Merchant reference not found', 400);
            }
            
            // Siparişi bul
            $order = Order::where('payment_reference', $merchantReference)->first();
            
            if (!$order) {
                return response('Order not found', 404);
            }
            
            // Ödeme durumunu güncelle
            switch (strtolower($paymentStatus)) {
                case 'success':
                case 'completed':
                case 'paid':
                    $order->payment_status = 'completed';
                    $order->status = 'confirmed';
                    break;
                    
                case 'failed':
                case 'cancelled':
                case 'error':
                    $order->payment_status = 'failed';
                    $order->status = 'cancelled';
                    break;
                    
                default:
                    $order->payment_status = 'pending';
                    break;
            }
            
            $order->save();
            
            Log::info('Payment Status Updated', [
                'order_id' => $order->id,
                'payment_status' => $order->payment_status
            ]);
            
            return response('OK', 200);
            
        } catch (\Exception $e) {
            Log::error('PayThor Callback Error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response('Error', 500);
        }
    }

    /**
     * Ödeme başarılı sayfası
     */
    public function success(Request $request)
    {
        $merchantReference = $request->merchant_reference;
        $order = null;
        
        if ($merchantReference) {
            $order = Order::where('payment_reference', $merchantReference)->first();
        }
        
        return view('payment.success', compact('order'));
    }

    /**
     * Ödeme iptal sayfası
     */
    public function cancel(Request $request)
    {
        $merchantReference = $request->merchant_reference;
        $order = null;
        
        if ($merchantReference) {
            $order = Order::where('payment_reference', $merchantReference)->first();
        }
        
        return view('payment.cancel', compact('order'));
    }
}
```

### 2. Migration Dosyası

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_reference')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'payment_status']);
        });
    }
}
```

### 3. Routes Tanımları (web.php)

```php
// Payment routes
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
```

### 4. Blade View Dosyaları

#### payment/form.blade.php (Ödeme Formu)

```html
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Ödeme Bilgileri</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('payment.process') }}">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" name="amount" value="{{ $order->total_amount }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ad Soyad</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Telefon</label>
                                    <input type="tel" class="form-control" name="phone" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-posta</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adres</label>
                            <textarea class="form-control" name="address" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Şehir</label>
                            <input type="text" class="form-control" name="city" required>
                        </div>

                        <div class="payment-summary bg-light p-3 rounded mb-3">
                            <h6>Sipariş Özeti</h6>
                            <div class="d-flex justify-content-between">
                                <span>Toplam Tutar:</span>
                                <strong>{{ number_format($order->total_amount, 2) }} TL</strong>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-lock"></i> Güvenli Ödeme Yap
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

#### payment/success.blade.php (Başarı Sayfası)

```html
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">Ödeme Başarılı!</h3>
                    <p class="text-muted">Siparişiniz başarıyla alınmıştır.</p>
                    
                    @if($order)
                        <div class="alert alert-info">
                            <strong>Sipariş No:</strong> #{{ $order->id }}<br>
                            <strong>Tutar:</strong> {{ number_format($order->total_amount, 2) }} TL
                        </div>
                    @endif

                    <a href="{{ route('home') }}" class="btn btn-primary">Ana Sayfaya Dön</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

#### payment/cancel.blade.php (İptal Sayfası)

```html
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">Ödeme İptal Edildi</h3>
                    <p class="text-muted">Ödeme işlemi iptal edildi veya başarısız oldu.</p>
                    
                    @if($order)
                        <div class="alert alert-warning">
                            <strong>Sipariş No:</strong> #{{ $order->id }}
                        </div>
                    @endif

                    <a href="{{ route('home') }}" class="btn btn-secondary">Ana Sayfaya Dön</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 🚀 Kurulum Adımları

### 1. Dosyaları Kopyalayın
- `PaymentController.php` dosyasını `app/Http/Controllers/` klasörüne kopyalayın
- View dosyalarını `resources/views/payment/` klasörüne kopyalayın
- Route tanımlarını `routes/web.php` dosyasına ekleyin

### 2. Migration Çalıştırın
```bash
php artisan make:migration add_payment_fields_to_orders_table
# Migration içeriğini yukarıdaki kodu kopyalayın
php artisan migrate
```

### 3. .env Dosyasını Güncelleyin
```env
PAYTHOR_TOKEN=your_paythor_token_here
```

### 4. User Modeline Paythor Token Alanı Ekleyin
```php
// User modelinde
protected $fillable = [
    // ... diğer alanlar
    'paythor_token',
];
```

### 5. Kullanım Örneği
```php
// Controller'da ödeme sayfasını göstermek için
public function showPaymentForm($orderId)
{
    $order = Order::findOrFail($orderId);
    return view('payment.form', compact('order'));
}

// Blade'de ödeme linkini oluşturmak için
<a href="{{ route('payment.form', ['order' => $order->id]) }}" class="btn btn-success">
    Ödeme Yap
</a>
```

---

## 🔧 Özelleştirme Seçenekleri

### PayThor Token Kaynağı Değiştirme
```php
// Controller'da getPaythorToken metodunu değiştirin
private function getPaythorToken()
{
    // Option 1: Admin user'dan al
    return User::where('role', 'admin')->first()->paythor_token ?? null;
    
    // Option 2: .env dosyasından al
    return env('PAYTHOR_TOKEN');
    
    // Option 3: Config dosyasından al
    return config('services.paythor.token');
}
```

### Sipariş Modeli Uyarlama
```php
// Kendi Order modelinizi kullanın
use App\Models\YourOrderModel as Order;
```

### Callback URL Özelleştirme
```php
// PayThor'dan gelen verilere göre callback metodunu uyarlayın
public function callback(Request $request)
{
    // Kendi callback logiklerinizi yazın
}
```

---

## 📞 Destek

Bu entegrasyon paketi, mevcut Laravel projenizden çıkarılmış PayThor ödeme sistemi kodlarını içerir. 

**Entegrasyon Desteği İçin:**
- E-mail: alienes.eren3024@gop.edu.tr
- GitHub: [@alieneseren](https://github.com/alieneseren)

**PayThor API Dokümantasyonu:**
- Dev API: `https://dev-api.paythor.com/`
- Production API: `https://api.paythor.com/`

---

✅ **Bu paket ile artık herhangi bir Laravel projesine PayThor ödeme sistemini entegre edebilirsiniz!**
