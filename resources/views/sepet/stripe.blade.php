
@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-6">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-6 text-center">
            <a href="{{ route('sepet.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 transition-colors duration-200 font-medium mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
                Geri Dön
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Kart ile Ödeme (Stripe)</h1>
            <p class="text-sm text-gray-600">Kart bilgilerinizi güvenle girin</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Ödeme Formu -->
            <div class="order-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 mb-1">Kart Bilgileri</h2>
                        <p class="text-sm text-gray-600">Kart bilgilerinizi güvenle girin</p>
                    </div>

                    <form id="payment-form" class="space-y-4">
                        @csrf
                        
                        <!-- Kişisel Bilgiler -->
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ad</label>
                                    <input type="text" value="{{ $user->ad ?? 'Test' }}" readonly 
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Soyad</label>
                                    <input type="text" value="{{ $user->soyad ?? 'User' }}" readonly 
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                    <input type="tel" value="{{ $user->telefon ?? '5555555555' }}" readonly 
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ülke</label>
                                    <input type="text" value="United States" readonly 
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Adres Bilgileri -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                                <input type="text" value="Merkezi mahallesi" readonly 
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Şehir</label>
                                    <input type="text" value="Antalya" readonly 
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Posta Kodu</label>
                                    <input type="text" value="07030" readonly 
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Kart Bilgileri Bölümü -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 mr-2">
                                    <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                                    <line x1="2" x2="22" y1="10" y2="10"></line>
                                </svg>
                                <h3 class="text-base font-semibold text-gray-900">Kart Bilgileri</h3>
                                <span class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full font-medium">Kullan</span>
                            </div>
                            
                            <div class="bg-white rounded-lg border-2 border-blue-300 p-3 mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kart Numarası, Son Kullanma ve CVC *</label>
                                <div id="card-element" class="py-2"></div>
                                <div id="card-errors" class="text-red-500 text-sm mt-1"></div>
                            </div>
                            
                            <div class="bg-white rounded-lg border border-gray-200 p-3">
                                <div class="text-sm text-gray-600 mb-1">
                                    <strong>Ödeme bilgileriniz SSL ile güvenle korunuyor</strong>
                                </div>
                                <div class="text-xs text-gray-500">
                                    Test Kartı: <strong>4242 4242 4242 4242</strong>, Son kullanma: herhangi gelecek tarih, CVC: herhangi 3 rakam
                                </div>
                            </div>
                        </div>

                        <!-- Ödeme Butonu -->
                        <button type="submit" id="submit" 
                                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white py-3 px-4 rounded-lg font-semibold text-base transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center shadow-md">
                            <span id="button-text">${{ number_format($toplam, 2) }} USD Öde</span>
                            <div id="spinner" class="hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        </button>
                    </form>
                </div>
            </div>
            <!-- Sipariş Özeti -->
            <div class="order-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Sipariş Özeti</h2>
                    
                    <div class="space-y-3 mb-4">
                        @foreach($sepetUrunler as $urun)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <img alt="{{ $urun->ad }}" class="w-12 h-12 object-cover rounded-lg flex-shrink-0 shadow-sm" src="{{ asset($urun->gorsel_url) }}">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 text-sm mb-0.5">{{ $urun->ad }}</h3>
                                <p class="text-sm text-gray-600">Adet: {{ $urun->adet }}</p>
                            </div>
                            <div class="text-base font-semibold text-gray-900">${{ number_format($urun->fiyat, 2) }} USD</div>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Ara Toplam</span>
                            <span class="font-medium">${{ number_format($araToplam, 2) }} USD</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Kargo</span>
                            <span class="font-medium">${{ number_format($kargo, 2) }} USD</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Vergi</span>
                            <span class="font-medium">${{ number_format($vergi, 2) }} USD</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-gray-900 pt-3 border-t border-gray-200">
                            <span>Toplam</span>
                            <span class="text-blue-600">${{ number_format($toplam, 2) }} USD</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements();
    
    const card = elements.create('card', {
        style: {
            base: {
                color: '#1f2937',
                fontFamily: 'system-ui, -apple-system, sans-serif',
                fontSize: '14px',
                fontWeight: '500',
                '::placeholder': { 
                    color: '#9ca3af',
                    fontWeight: '400'
                },
                iconColor: '#4361ee',
                lineHeight: '20px',
            },
            focus: {
                color: '#1f2937',
                iconColor: '#4361ee',
            },
            complete: { 
                color: '#059669',
                iconColor: '#059669'
            },
            invalid: { 
                color: '#dc2626',
                iconColor: '#dc2626'
            }
        },
        hidePostalCode: true
    });
    
    card.mount('#card-element');

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');
    const cardErrors = document.getElementById('card-errors');

    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        submitButton.disabled = true;
        buttonText.textContent = 'İşleniyor...';
        spinner.classList.remove('hidden');
        cardErrors.textContent = '';
        
        try {
            const response = await fetch("{{ route('sepet.stripe.payment_intent') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({})
            });

            const {clientSecret, error: serverError} = await response.json();

            if (serverError) {
                throw new Error(serverError);
            }

            const {error, paymentIntent} = await stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: card,
                    billing_details: {
                        name: '{{ $user->ad ?? "Test" }} {{ $user->soyad ?? "User" }}',
                        email: '{{ $user->email ?? "test@example.com" }}'
                    }
                }
            });

            if (error) {
                cardErrors.textContent = error.message;
            } else if (paymentIntent.status === 'succeeded') {
                window.location.href = "{{ route('odeme.basarili') }}";
                return;
            }
        } catch (e) {
            cardErrors.textContent = 'Bir hata oluştu. Lütfen tekrar deneyin.';
        }
        
        submitButton.disabled = false;
        buttonText.textContent = '${{ number_format($toplam, 2) }} USD Öde';
        spinner.classList.add('hidden');
    });

    card.on('change', function(event) {
        cardErrors.textContent = event.error ? event.error.message : '';
    });

    // Card element focus efektleri
    card.on('focus', function() {
        document.getElementById('card-element').parentElement.classList.add('ring-2', 'ring-blue-500', 'border-blue-500');
        document.getElementById('card-element').parentElement.classList.remove('border-blue-300');
    });

    card.on('blur', function() {
        document.getElementById('card-element').parentElement.classList.remove('ring-2', 'ring-blue-500', 'border-blue-500');
        document.getElementById('card-element').parentElement.classList.add('border-blue-300');
    });
</script>
@endsection


