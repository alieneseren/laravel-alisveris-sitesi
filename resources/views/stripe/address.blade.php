@extends('layouts.app')

@section('title', 'Teslimat Bilgileri - Stripe Ödeme')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Progress Bar -->
            <div class="card border-0 mb-4">
                <div class="card-body p-2">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 50%"></div>
                    </div>
                    <div class="row text-center mt-2">
                        <div class="col-6">
                            <span class="badge bg-primary">1. Teslimat Bilgileri</span>
                        </div>
                        <div class="col-6">
                            <span class="badge bg-light text-dark">2. Kart Bilgileri</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Sol Taraf - Sipariş Özeti -->
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Sipariş Özeti
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($sepetUranlari as $item)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <h6 class="mb-1">{{ $item['urun']->ad }}</h6>
                                    <small class="text-muted">
                                        {{ $item['miktar'] }} adet × {{ number_format($item['urun']->fiyat, 2) }} ₺
                                    </small>
                                </div>
                                <span class="fw-bold">{{ number_format($item['ara_toplam'], 2) }} ₺</span>
                            </div>
                            @endforeach
                            
                            <div class="d-flex justify-content-between align-items-center pt-3">
                                <h5 class="mb-0 text-primary">Toplam:</h5>
                                <h4 class="mb-0 text-success">{{ number_format($toplam, 2) }} ₺</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Güvenlik Rozetleri -->
                    <div class="card shadow-sm mt-3">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-3">Güvenli Ödeme</h6>
                            <div class="row">
                                <div class="col-4">
                                    <i class="fas fa-shield-alt text-success fs-4"></i>
                                    <small class="d-block text-muted">SSL Güvenlik</small>
                                </div>
                                <div class="col-4">
                                    <i class="fab fa-stripe text-primary fs-4"></i>
                                    <small class="d-block text-muted">Stripe</small>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-lock text-warning fs-4"></i>
                                    <small class="d-block text-muted">256-bit SSL</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Taraf - Teslimat Bilgileri Formu -->
                <div class="col-md-7">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-gradient-primary text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Teslimat Bilgileri
                            </h4>
                            <small class="opacity-75">Lütfen teslimat adresinizi girin</small>
                        </div>
                        
                        <div class="card-body p-4">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form id="address-form" action="{{ route('stripe.address.save') }}" method="POST">
                                @csrf
                                
                                <!-- Müşteri Bilgileri -->
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>
                                    Müşteri Bilgileri
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ad_soyad" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg" id="ad_soyad" name="ad_soyad" 
                                               value="{{ old('ad_soyad', (auth()->user()->ad ?? '') . ' ' . (auth()->user()->soyad ?? '')) }}" required>
                                        @error('ad_soyad')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">E-posta <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                               value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="telefon" class="form-label">Telefon <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control form-control-lg" id="telefon" name="telefon" 
                                               value="{{ old('telefon') }}" required placeholder="0555 123 45 67">
                                        @error('telefon')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Teslimat Adresi -->
                                <h5 class="text-primary mb-3 mt-4">
                                    <i class="fas fa-truck me-2"></i>
                                    Teslimat Adresi
                                </h5>
                                
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="adres" class="form-label">Adres <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="adres" name="adres" rows="3" required 
                                                  placeholder="Mahalle, sokak, bina numarası ve diğer adres detayları">{{ old('adres') }}</textarea>
                                        @error('adres')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="il" class="form-label">İl <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="il" name="il" 
                                               value="{{ old('il') }}" required placeholder="İstanbul">
                                        @error('il')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="ilce" class="form-label">İlçe <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="ilce" name="ilce" 
                                               value="{{ old('ilce') }}" required placeholder="Kadıköy">
                                        @error('ilce')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="posta_kodu" class="form-label">Posta Kodu</label>
                                        <input type="text" class="form-control" id="posta_kodu" name="posta_kodu" 
                                               value="{{ old('posta_kodu') }}" placeholder="34000">
                                        @error('posta_kodu')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label for="aciklama" class="form-label">Adres Açıklaması</label>
                                        <input type="text" class="form-control" id="aciklama" name="aciklama" 
                                               value="{{ old('aciklama') }}" placeholder="Örn: Ev, İş, Kapıcıya teslim edilebilir">
                                        @error('aciklama')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Devam Butonu -->
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg py-3" id="continue-button">
                                        <span id="button-text">
                                            <i class="fas fa-credit-card me-2"></i>
                                            Kart Bilgilerine Geç
                                        </span>
                                        <div id="spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </button>
                                    
                                    <a href="{{ route('sepet.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Sepete Geri Dön
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.form-control-lg {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control-lg:focus, .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
}

.btn-lg {
    border-radius: 10px;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.alert {
    border-radius: 10px;
    border: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('address-form');
    const submitButton = document.getElementById('continue-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');

    form.addEventListener('submit', function(e) {
        // Buton durumunu güncelle
        submitButton.disabled = true;
        buttonText.classList.add('d-none');
        spinner.classList.remove('d-none');
    });

    // Form doğrulaması için Bootstrap validation
    form.classList.add('needs-validation');
    form.addEventListener('input', function(e) {
        if (e.target.checkValidity()) {
            e.target.classList.remove('is-invalid');
            e.target.classList.add('is-valid');
        } else {
            e.target.classList.remove('is-valid');
            e.target.classList.add('is-invalid');
        }
    });
});
</script>
@endsection
