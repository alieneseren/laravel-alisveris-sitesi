@extends('layouts.app')

@section('title', 'Ödeme Başarılı')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body text-center p-5">
                    <!-- Başarı İkonu -->
                    <div class="success-icon mb-4">
                        <div class="checkmark-circle">
                            <div class="checkmark"></div>
                        </div>
                    </div>

                    <!-- Başarı Mesajı -->
                    <h1 class="text-success mb-3">Ödeme Başarılı!</h1>
                    <p class="lead text-muted mb-4">
                        Siparişiniz başarıyla oluşturuldu ve ödemeniz alındı.
                    </p>

                    <!-- Sipariş Bilgileri -->
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-8">
                            <div class="bg-light rounded p-4">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-receipt me-2"></i>
                                    Sipariş Detayları
                                </h5>
                                
                                <div class="row text-start">
                                    <div class="col-6">
                                        <strong>Sipariş No:</strong>
                                    </div>
                                    <div class="col-6">
                                        #{{ $siparis->id }}
                                    </div>
                                </div>
                                
                                <div class="row text-start">
                                    <div class="col-6">
                                        <strong>Tarih:</strong>
                                    </div>
                                    <div class="col-6">
                                        {{ $siparis->created_at->format('d.m.Y H:i') }}
                                    </div>
                                </div>
                                
                                <div class="row text-start">
                                    <div class="col-6">
                                        <strong>Toplam Tutar:</strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-success fw-bold">{{ number_format($siparis->toplam_tutar, 2) }} ₺</span>
                                    </div>
                                </div>
                                
                                <div class="row text-start">
                                    <div class="col-6">
                                        <strong>Durum:</strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="badge bg-success">Onaylandı</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- İkon Bilgilendirme -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                                <h6>E-posta Gönderildi</h6>
                                <small class="text-muted">Sipariş onayınız e-posta adresinize gönderildi</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <i class="fas fa-truck fa-2x text-info mb-2"></i>
                                <h6>Kargo Hazırlığı</h6>
                                <small class="text-muted">Siparişiniz kargo için hazırlanacak</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-box">
                                <i class="fas fa-bell fa-2x text-warning mb-2"></i>
                                <h6>Bildirim</h6>
                                <small class="text-muted">Kargo kodu SMS ile gönderilecek</small>
                            </div>
                        </div>
                    </div>

                    <!-- Aksiyon Butonları -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('siparislerim') }}" class="btn btn-primary btn-lg me-md-2">
                            <i class="fas fa-list me-2"></i>
                            Siparişlerimi Görüntüle
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-home me-2"></i>
                            Ana Sayfaya Dön
                        </a>
                    </div>

                    <!-- Sosyal Paylaşım -->
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="text-muted mb-3">Deneyiminizi paylaşın</h6>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-info">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-success">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-icon {
    display: inline-block;
}

.checkmark-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4CAF50, #45a049);
    position: relative;
    margin: 0 auto;
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
    animation: scaleIn 0.5s ease-in-out;
}

.checkmark {
    width: 25px;
    height: 45px;
    border: solid white;
    border-width: 0 4px 4px 0;
    transform: rotate(45deg);
    position: absolute;
    left: 28px;
    top: 12px;
    animation: checkmarkSlide 0.3s ease-in-out 0.3s both;
}

@keyframes scaleIn {
    0% {
        transform: scale(0);
    }
    100% {
        transform: scale(1);
    }
}

@keyframes checkmarkSlide {
    0% {
        opacity: 0;
        transform: translateY(10px) rotate(45deg);
    }
    100% {
        opacity: 1;
        transform: translateY(0) rotate(45deg);
    }
}

.info-box {
    padding: 20px;
    border-radius: 10px;
    background: rgba(248, 249, 250, 0.8);
    transition: all 0.3s ease;
}

.info-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.card {
    border-radius: 20px;
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.btn-lg {
    border-radius: 10px;
    padding: 12px 30px;
    font-weight: 600;
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

.bg-light {
    border-radius: 15px;
    border: 1px solid rgba(0,0,0,0.05);
}
</style>

<script>
// Sayfa yüklendiğinde konfeti efekti (isteğe bağlı)
document.addEventListener('DOMContentLoaded', function() {
    // Basit bir başarı animasyonu
    setTimeout(function() {
        document.querySelector('.checkmark-circle').style.transform = 'scale(1.1)';
        setTimeout(function() {
            document.querySelector('.checkmark-circle').style.transform = 'scale(1)';
        }, 200);
    }, 800);
});
</script>
@endsection
