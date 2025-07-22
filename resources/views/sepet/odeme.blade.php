@extends('layouts.app')

@section('title', 'Ödeme')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-credit-card"></i> Ödeme Bilgileri</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('sepet.odeme.yap') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ad_soyad" class="form-label">Ad Soyad</label>
                                    <input type="text" class="form-control" id="ad_soyad" name="ad_soyad" 
                                           value="{{ (auth()->user()->ad ?? '') . ' ' . (auth()->user()->soyad ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefon" class="form-label">Telefon</label>
                                    <input type="tel" class="form-control" id="telefon" name="telefon" 
                                           value="{{ auth()->user()->telefon ?? '' }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ auth()->user()->email ?? '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="adres" class="form-label">Teslimat Adresi</label>
                            <textarea class="form-control" id="adres" name="adres" rows="3" required placeholder="Tam adresinizi giriniz..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="il" class="form-label">İl</label>
                                    <input type="text" class="form-control" id="il" name="il" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ilce" class="form-label">İlçe</label>
                                    <input type="text" class="form-control" id="ilce" name="ilce" required>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="payment-info bg-light p-3 rounded">
                            <h6><i class="fas fa-shield-alt text-success"></i> Güvenli Ödeme</h6>
                            <p class="mb-2"><small>Ödemeniz Paythor güvenli ödeme sistemi ile korunmaktadır.</small></p>
                            <div class="payment-methods">
                                <i class="fab fa-cc-visa fa-2x me-2 text-primary"></i>
                                <i class="fab fa-cc-mastercard fa-2x me-2 text-warning"></i>
                                <i class="fas fa-credit-card fa-2x me-2 text-info"></i>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('sepet') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Sepete Dön
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-lock"></i> Güvenli Ödeme Yap
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-shopping-bag"></i> Sipariş Özeti</h6>
                </div>
                <div class="card-body">
                    @foreach($sepetUranlari as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="mb-0">{{ Str::limit($item['urun']->ad, 25) }}</h6>
                                <small class="text-muted">{{ $item['miktar'] }} adet</small>
                            </div>
                            <span class="fw-bold">{{ number_format($item['ara_toplam'], 2) }} ₺</span>
                        </div>
                        <hr class="my-2">
                    @endforeach

                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Toplam:</h5>
                        <h5 class="mb-0 text-success">{{ number_format($toplam, 2) }} ₺</h5>
                    </div>

                    <div class="mt-3">
                        <div class="shipping-info bg-light p-2 rounded">
                            <small>
                                <i class="fas fa-truck text-primary"></i> <strong>Ücretsiz Kargo</strong><br>
                                <i class="fas fa-calendar text-info"></i> Tahmini Teslimat: 2-3 iş günü
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle text-info"></i> Önemli Bilgiler</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small><i class="fas fa-check text-success"></i> 14 gün iade garantisi</small></li>
                        <li><small><i class="fas fa-check text-success"></i> Güvenli ödeme</small></li>
                        <li><small><i class="fas fa-check text-success"></i> Hızlı teslimat</small></li>
                        <li><small><i class="fas fa-check text-success"></i> 7/24 müşteri hizmetleri</small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-methods i {
    opacity: 0.8;
}

.payment-info {
    border: 1px solid #e9ecef;
}

.shipping-info {
    border: 1px solid #e9ecef;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}
</style>
@endsection
