@extends('layouts.app')@section('title', 'Stripe ile Ödeme')@section('content')<div class="container mt-4">    <div class="row justify-content-center">        <div class="col-lg-10">            <!-- Stripe Ödeme Formu -->            <div class="card shadow-lg border-0">                <div class="card-header bg-gradient-primary text-white">                    <h4 class="mb-0">                        <i class="fab fa-stripe-s me-2"></i>                        Güvenli Ödeme                    </h4>                    <small class="opacity-75">Kart bilgilerinizi girerek ödeme yapın</small>                </div>                                <div class="card-body p-4">                    @if(session('error'))                        <div class="alert alert-danger alert-dismissible fade show" role="alert">                            <i class="fas fa-exclamation-triangle me-2"></i>                            {{ session('error') }}                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>                        </div>                    @endif                    <!-- Ödeme Hatası Gösterme -->                    <div id="card-errors" class="alert alert-danger d-none" role="alert">                        <i class="fas fa-exclamation-triangle me-2"></i>                        <span id="error-message"></span>                    </div>                    <!-- Başarı Mesajı -->                    <div id="payment-success" class="alert alert-success d-none" role="alert">                        <i class="fas fa-check-circle me-2"></i>                        <span>Ödemeniz başarıyla tamamlandı! Sipariş oluşturuluyor...</span>                    </div>                    <div class="row">                        <!-- Sol Taraf - Sipariş Özeti -->                        <div class="col-md-5">                            <h5 class="text-primary mb-3">                                <i class="fas fa-file-invoice me-2"></i>                                Sipariş Özeti                            </h5>                            <div class="bg-light rounded p-3 mb-4">                                @foreach($sepetUranlari as $item)                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">                                    <div>                                        <h6 class="mb-1">{{ $item['urun']->ad }}</h6>                                        <small class="text-muted">                                            {{ $item['miktar'] }} adet × {{ number_format($item['urun']->fiyat, 2) }} ₺                                        </small>                                    </div>                                    <span class="fw-bold">{{ number_format($item['ara_toplam'], 2) }} ₺</span>                                </div>                                @endforeach                                                                <div class="d-flex justify-content-between align-items-center pt-3">                                    <h5 class="mb-0 text-primary">Toplam:</h5>                                    <h4 class="mb-0 text-success">{{ number_format($toplam, 2) }} ₺</h4>                                </div>                            </div>                                                        <!-- Güvenlik Rozetleri -->                            <div class="d-flex justify-content-center mt-3">                                <div class="text-center me-3">                                    <i class="fas fa-shield-alt text-success fs-4"></i>                                    <small class="d-block text-muted">SSL Güvenlik</small>                                </div>                                <div class="text-center me-3">                                    <i class="fab fa-stripe text-primary fs-4"></i>                                    <small class="d-block text-muted">Stripe Güvencesi</small>                                </div>                                <div class="text-center">                                    <i class="fas fa-lock text-warning fs-4"></i>                                    <small class="d-block text-muted">Şifreli Ödeme</small>                                </div>                            </div>                        </div>                        <!-- Sağ Taraf - Ödeme Formu -->                        <div class="col-md-7">                            <form id="payment-form">                                @csrf                                <h5 class="text-primary mb-3">                                    <i class="fas fa-credit-card me-2"></i>                                    Kart Bilgileri                                </h5>                                <!-- Müşteri Bilgileri -->                                <div class="row mb-3">                                    <div class="col-md-6 mb-3">                                        <label for="ad_soyad" class="form-label">Ad Soyad <span class="text-danger">*</span></label>                                        <input type="text" class="form-control form-control-lg" id="ad_soyad" name="ad_soyad"                                                value="{{ auth()->user()->ad ?? '' }} {{ auth()->user()->soyad ?? '' }}" required>                                    </div>                                                                        <div class="col-md-6 mb-3">                                        <label for="email" class="form-label">E-posta <span class="text-danger">*</span></label>                                        <input type="email" class="form-control form-control-lg" id="email" name="email"                                                value="{{ auth()->user()->email ?? '' }}" required>                                    </div>                                </div>                                <!-- Kart Bilgileri Stripe Elements -->                                <div class="mb-3">                                    <label class="form-label">Kart Bilgileri <span class="text-danger">*</span></label>                                    <div id="card-element" class="form-control form-control-lg stripe-card-element">                                        <!-- Stripe Elements kart input'u buraya gelecek -->                                    </div>                                </div>                                <!-- Fatura Adresi -->                                <h6 class="text-secondary mb-3 mt-4">                                    <i class="fas fa-map-marker-alt me-2"></i>                                    Fatura Adresi                                </h6>                                                                <div class="row">                                    <div class="col-md-6 mb-3">                                        <label for="telefon" class="form-label">Telefon <span class="text-danger">*</span></label>                                        <input type="tel" class="form-control" id="telefon" name="telefon" required>                                    </div>                                                                        <div class="col-12 mb-3">                                        <label for="adres" class="form-label">Adres <span class="text-danger">*</span></label>                                        <textarea class="form-control" id="adres" name="adres" rows="2" required                                                   placeholder="Mahalle, sokak, bina numarası"></textarea>                                    </div>                                                                        <div class="col-md-4 mb-3">                                        <label for="il" class="form-label">İl <span class="text-danger">*</span></label>                                        <input type="text" class="form-control" id="il" name="il" required>                                    </div>                                                                        <div class="col-md-4 mb-3">                                        <label for="ilce" class="form-label">İlçe <span class="text-danger">*</span></label>                                        <input type="text" class="form-control" id="ilce" name="ilce" required>                                    </div>                                                                        <div class="col-md-4 mb-3">                                        <label for="posta_kodu" class="form-label">Posta Kodu</label>                                        <input type="text" class="form-control" id="posta_kodu" name="posta_kodu">                                    </div>                                </div>                                <!-- Ödeme Butonu -->                                <div class="d-grid gap-2 mt-4">                                    <button type="submit" class="btn btn-primary btn-lg py-3" id="submit-button">                                        <i class="fas fa-lock me-2"></i>                                        <span id="button-text">Güvenli Ödeme Yap - {{ number_format($toplam, 2) }} ₺</span>                                        <div id="spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status">                                            <span class="visually-hidden">Loading...</span>                                        </div>                                    </button>                                                                        <a href="{{ route('sepet.index') }}" class="btn btn-outline-secondary">                                        <i class="fas fa-arrow-left me-2"></i>                                        Sepete Geri Dön                                    </a>                                </div>                            </form>                        </div>                    </div>                </div>                                <!-- Footer -->                <div class="card-footer bg-light text-center">                    <small class="text-muted">                        <i class="fas fa-info-circle me-1"></i>                        Kart bilgileriniz Stripe tarafından güvenli bir şekilde işlenir. Bilgileriniz sunucumuzda saklanmaz.                    </small>                </div>            </div>        </div>    </div></div><style>.bg-gradient-primary {    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);}.card {    border-radius: 15px;    overflow: hidden;}.form-control-lg {    border-radius: 10px;    border: 2px solid #e9ecef;    transition: all 0.3s ease;}.form-control-lg:focus {    border-color: #667eea;    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);}.btn-lg {    border-radius: 10px;    font-weight: 600;    letter-spacing: 0.5px;    transition: all 0.3s ease;}.btn-primary {    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);    border: none;}.btn-primary:hover {    transform: translateY(-2px);    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);}.badge {    border-radius: 8px;}.alert {    border-radius: 10px;    border: none;}.bg-light {    background-color: #f8f9fa !important;    border-radius: 10px;}.stripe-card-element {    padding: 15px;    background: white;    border-radius: 8px;    transition: border-color 0.3s ease;}.stripe-card-element.StripeElement--focus {    border-color: #667eea;    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);}.stripe-card-element.StripeElement--invalid {    border-color: #dc3545;}.stripe-card-element.StripeElement--complete {    border-color: #28a745;}</style><!-- Stripe JavaScript SDK --><script src="https://js.stripe.com/v3/"></script><script>document.addEventListener('DOMContentLoaded', function() {    // Stripe public key    const stripe = Stripe('{{ config("services.stripe.key") }}');    const elements = stripe.elements();        // Türkçe stil    const style = {        base: {            color: '#32325d',            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',            fontSmoothing: 'antialiased',            fontSize: '16px',            '::placeholder': {                color: '#aab7c4'            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    // Kart elementi oluştur
    const cardElement = elements.create('card', {
        style: style,
        hidePostalCode: true
    });
    
    cardElement.mount('#card-element');

    // Form elementleri
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');
    const errorElement = document.getElementById('card-errors');
    const successElement = document.getElementById('payment-success');

    // Kart değişikliklerini dinle
    cardElement.on('change', function(event) {
        if (event.error) {
            showError(event.error.message);
        } else {
            hideError();
        }
    });

    // Form submit
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        // Buton durumunu güncelle
        setLoading(true);
        
        // Form doğrulaması
        if (!validateForm()) {
            setLoading(false);
            return;
        }

        try {
            // Payment Intent oluştur
            const response = await fetch('{{ route("stripe.create.payment.intent") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    amount: {{ $toplam * 100 }}, // Kuruş cinsinden
                    customer_data: getFormData()
                })
            });

            const paymentData = await response.json();
            
            if (!response.ok) {
                throw new Error(paymentData.message || 'Ödeme hazırlanırken hata oluştu');
            }

            // Ödeme confirm et
            const {error, paymentIntent} = await stripe.confirmCardPayment(
                paymentData.client_secret,
                {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: document.getElementById('ad_soyad').value,
                            email: document.getElementById('email').value,
                            phone: document.getElementById('telefon').value,
                            address: {
                                line1: document.getElementById('adres').value,
                                city: document.getElementById('il').value,
                                state: document.getElementById('ilce').value,
                                postal_code: document.getElementById('posta_kodu').value,
                                country: 'TR'
                            }
                        }
                    }
                }
            );

            if (error) {
                showError(error.message);
                setLoading(false);
            } else {
                // Ödeme başarılı
                showSuccess();
                
                // Sipariş oluştur
                const orderResponse = await fetch('{{ route("stripe.create.order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        payment_intent_id: paymentIntent.id,
                        customer_data: getFormData()
                    })
                });

                const orderData = await orderResponse.json();
                
                if (orderResponse.ok) {
                    // Başarı sayfasına yönlendir
                    setTimeout(() => {
                        window.location.href = '{{ route("stripe.success") }}?order_id=' + orderData.order_id;
                    }, 2000);
                } else {
                    showError('Sipariş oluşturulurken hata oluştu: ' + orderData.message);
                    setLoading(false);
                }
            }

        } catch (error) {
            showError('Ödeme sırasında hata oluştu: ' + error.message);
            setLoading(false);
        }
    });

    // Yardımcı fonksiyonlar
    function setLoading(loading) {
        submitButton.disabled = loading;
        if (loading) {
            buttonText.classList.add('d-none');
            spinner.classList.remove('d-none');
        } else {
            buttonText.classList.remove('d-none');
            spinner.classList.add('d-none');
        }
    }

    function showError(message) {
        errorElement.querySelector('#error-message').textContent = message;
        errorElement.classList.remove('d-none');
        successElement.classList.add('d-none');
    }

    function hideError() {
        errorElement.classList.add('d-none');
    }

    function showSuccess() {
        successElement.classList.remove('d-none');
        errorElement.classList.add('d-none');
    }

    function validateForm() {
        const requiredFields = ['ad_soyad', 'email', 'telefon', 'adres', 'il', 'ilce'];
        let isValid = true;

        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    function getFormData() {
        return {
            ad_soyad: document.getElementById('ad_soyad').value,
            email: document.getElementById('email').value,
            telefon: document.getElementById('telefon').value,
            adres: document.getElementById('adres').value,
            il: document.getElementById('il').value,
            ilce: document.getElementById('ilce').value,
            posta_kodu: document.getElementById('posta_kodu').value
        };
    }
});
</script>
@endsection
