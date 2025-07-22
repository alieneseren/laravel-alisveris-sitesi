@extends('layouts.app')

@section('title', 'Ödeme Başarısız')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white text-center">
                    <h4><i class="fas fa-times-circle"></i> Ödeme Başarısız!</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-times-circle text-danger" style="font-size: 80px;"></i>
                    </div>
                    
                    <h5 class="card-title">Ödeme işlemi tamamlanamadı</h5>
                    
                    @if($siparis)
                    <div class="alert alert-warning">
                        <strong>Sipariş No:</strong> #{{ $siparis->id }}<br>
                        <strong>Sipariş Tutarı:</strong> {{ number_format($siparis->toplam_tutar, 2) }} TL<br>
                        <strong>Ödeme Durumu:</strong> 
                        <span class="badge badge-danger">
                            {{ $siparis->payment_status == 'failed' ? 'Başarısız' : 'İptal Edildi' }}
                        </span>
                    </div>
                    @endif
                    
                    <p class="card-text">
                        Ödeme işleminiz tamamlanamadı. Lütfen ödeme bilgilerinizi kontrol edip tekrar deneyiniz.
                    </p>
                    
                    <div class="alert alert-info">
                        <strong>Olası Sebepler:</strong><br>
                        • Kredi kartı bilgilerinde hata<br>
                        • Yetersiz bakiye<br>
                        • İşlem limit aşımı<br>
                        • Ağ bağlantı problemi
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('sepet.odeme') }}" class="btn btn-primary mr-2">
                            <i class="fas fa-credit-card"></i> Tekrar Dene
                        </a>
                        <a href="{{ route('sepet') }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-shopping-cart"></i> Sepete Dön
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home"></i> Ana Sayfa
                        </a>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            Problem devam ederse lütfen müşteri hizmetleri ile iletişime geçiniz.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
