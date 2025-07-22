@extends('layouts.app')

@section('title', 'Sipariş Yönetimi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-shopping-cart"></i> Sipariş Yönetimi</h2>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Tüm Siparişler ({{ $siparisler->total() }} sipariş)</h5>
                </div>
                <div class="card-body">
                    @if($siparisler->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5>Henüz sipariş bulunmuyor</h5>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Sipariş No</th>
                                        <th>Müşteri</th>
                                        <th>Toplam Tutar</th>
                                        <th>Durum</th>
                                        <th>Ürün Sayısı</th>
                                        <th>Sipariş Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siparisler as $siparis)
                                        <tr>
                                            <td>
                                                <strong>#{{ $siparis->id }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $siparis->kullanici->ad ?? 'Kullanıcı yok' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $siparis->kullanici->eposta ?? '' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($siparis->toplam_tutar, 2) }} ₺</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $durumBadge = [
                                                        'beklemede' => 'warning',
                                                        'onaylandi' => 'info', 
                                                        'hazirlaniyor' => 'primary',
                                                        'kargoda' => 'secondary',
                                                        'teslim_edildi' => 'success',
                                                        'iptal_edildi' => 'danger'
                                                    ][$siparis->durum] ?? 'secondary';
                                                    
                                                    $durumAdi = [
                                                        'beklemede' => 'Beklemede',
                                                        'onaylandi' => 'Onaylandı',
                                                        'hazirlaniyor' => 'Hazırlanıyor',
                                                        'kargoda' => 'Kargoda',
                                                        'teslim_edildi' => 'Teslim Edildi',
                                                        'iptal_edildi' => 'İptal Edildi'
                                                    ][$siparis->durum] ?? 'Bilinmiyor';
                                                @endphp
                                                <span class="badge bg-{{ $durumBadge }}">{{ $durumAdi }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $siparis->siparisUrunleri->sum('miktar') }}
                                                </span>
                                            </td>
                                            <td>{{ $siparis->created_at->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-info" title="Detayları Görüntüle">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-warning" title="Durumu Güncelle">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-primary" title="Yazdır">
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $siparisler->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Son Siparişler -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Son 24 Saat İçindeki Siparişler</h5>
                </div>
                <div class="card-body">
                    @php
                        $sonSiparisler = $siparisler->where('created_at', '>=', now()->subDay());
                    @endphp
                    
                    @if($sonSiparisler->isNotEmpty())
                        <div class="row">
                            @foreach($sonSiparisler->take(6) as $siparis)
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6>#{{ $siparis->id }} - {{ $siparis->kullanici->ad ?? 'Kullanıcı yok' }}</h6>
                                            <h5 class="text-success">{{ number_format($siparis->toplam_tutar, 2) }} ₺</h5>
                                            <small class="text-muted">{{ $siparis->created_at->diffForHumans() }}</small>
                                            <div class="mt-2">
                                                @php
                                                    $durumBadge = [
                                                        'beklemede' => 'warning',
                                                        'onaylandi' => 'info', 
                                                        'hazirlaniyor' => 'primary',
                                                        'kargoda' => 'secondary',
                                                        'teslim_edildi' => 'success',
                                                        'iptal_edildi' => 'danger'
                                                    ][$siparis->durum] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $durumBadge }}">{{ ucfirst($siparis->durum) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <p>Son 24 saatte sipariş bulunmuyor</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sipariş Durum İstatistikleri -->
    <div class="row mt-4">
        <div class="col-md-2">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-warning">{{ $siparisler->where('durum', 'beklemede')->count() }}</h4>
                    <small class="text-muted">Beklemede</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-info">{{ $siparisler->where('durum', 'onaylandi')->count() }}</h4>
                    <small class="text-muted">Onaylandı</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-primary">{{ $siparisler->where('durum', 'hazirlaniyor')->count() }}</h4>
                    <small class="text-muted">Hazırlanıyor</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-secondary">{{ $siparisler->where('durum', 'kargoda')->count() }}</h4>
                    <small class="text-muted">Kargoda</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-success">{{ $siparisler->where('durum', 'teslim_edildi')->count() }}</h4>
                    <small class="text-muted">Teslim Edildi</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="text-danger">{{ $siparisler->where('durum', 'iptal_edildi')->count() }}</h4>
                    <small class="text-muted">İptal Edildi</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
