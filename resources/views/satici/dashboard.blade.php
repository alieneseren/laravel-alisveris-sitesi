@extends('layouts.app')

@section('title', 'Satıcı Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-store"></i> Satıcı Paneli</h2>
                @if(!$magaza)
                    <a href="{{ route('satici.magaza.olustur') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Mağaza Oluştur
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if(!$magaza)
        <div class="row">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h4>Henüz Mağazanız Yok</h4>
                        <p class="text-muted">Ürün satışı yapabilmek için önce bir mağaza oluşturmanız gerekiyor.</p>
                        <a href="{{ route('satici.magaza.olustur') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus"></i> Mağaza Oluştur
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Mağaza Bilgileri -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">{{ $magaza->magaza_adi }}</h4>
                                <p class="text-muted mb-0">{{ $magaza->magaza_aciklamasi }}</p>
                                <small class="text-muted">Mağaza oluşturulma: {{ $magaza->created_at->format('d.m.Y') }}</small>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i> Mağaza Düzenle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- İstatistikler -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-box fa-2x text-primary mb-2"></i>
                        <h3 class="text-primary">{{ $stats['toplam_urun'] }}</h3>
                        <small class="text-muted">Toplam Ürün</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <h3 class="text-success">{{ $stats['aktif_urun'] }}</h3>
                        <small class="text-muted">Aktif Ürün</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-lira-sign fa-2x text-warning mb-2"></i>
                        <h3 class="text-warning">₺{{ number_format($stats['toplam_satis'], 2) }}</h3>
                        <small class="text-muted">Toplam Satış</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-clock fa-2x text-info mb-2"></i>
                        <h3 class="text-info">{{ $stats['bekleyen_siparis'] }}</h3>
                        <small class="text-muted">Bekleyen Sipariş</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı Erişim -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-box"></i> Ürün Yönetimi</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('satici.urun.ekle') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Yeni Ürün Ekle
                            </a>
                            <a href="{{ route('satici.urunler') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list"></i> Tüm Ürünleri Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar"></i> Satış Raporu</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('satici.siparisler') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-bag"></i> Sipariş Geçmişim
                            </a>
                            <a href="#" class="btn btn-outline-info">
                                <i class="fas fa-chart-line"></i> Satış Analizi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
