@extends('layouts.app')

@section('title', 'Ödeme Başarılı')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white text-center">
                    <h4><i class="fas fa-check-circle"></i> Ödeme Başarılı!</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    
                    <h5 class="card-title">Siparişiniz başarıyla alındı!</h5>
                    
                    @if($siparis)
                    <div class="alert alert-info">
                        <strong>Sipariş No:</strong> #{{ $siparis->id }}<br>
                        <strong>Sipariş Tutarı:</strong> {{ number_format($siparis->toplam_tutar, 2) }} TL<br>
                        <strong>Sipariş Tarihi:</strong> {{ $siparis->created_at->format('d.m.Y H:i') }}<br>
                        <strong>Ödeme Durumu:</strong> 
                        <span class="badge badge-success">
                            {{ $siparis->payment_status == 'completed' ? 'Tamamlandı' : 'İşleniyor' }}
                        </span>
                    </div>
                    @endif
                    
                    <p class="card-text">
                        Siparişiniz onaylandı ve işleme alındı. Kargo takip bilgileriniz e-posta adresinize gönderilecektir.
                    </p>
                    
                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary mr-2">
                            <i class="fas fa-home"></i> Ana Sayfaya Dön
                        </a>
                        @auth
                        <a href="{{ route('profil') }}" class="btn btn-secondary">
                            <i class="fas fa-user"></i> Siparişlerimi Görüntüle
                        </a>
                        @endauth
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            Herhangi bir sorunuz varsa bizimle iletişime geçebilirsiniz.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
