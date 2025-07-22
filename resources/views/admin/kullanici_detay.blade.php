@extends('layouts.app')

@section('title', 'Kullanıcı Detayları')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-user"></i> Kullanıcı Detayları</h2>
                <div>
                    <a href="{{ route('admin.kullanicilar') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Kullanıcılar
                    </a>
                    <a href="{{ route('admin.kullanici.duzenle', $kullanici->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Düzenle
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Kullanıcı Bilgileri</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td>{{ $kullanici->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Ad Soyad:</strong></td>
                            <td>{{ $kullanici->ad }}</td>
                        </tr>
                        <tr>
                            <td><strong>E-posta:</strong></td>
                            <td>{{ $kullanici->eposta }}</td>
                        </tr>
                        <tr>
                            <td><strong>Rol:</strong></td>
                            <td>
                                @php
                                    $rolBadge = [
                                        'yonetici' => 'danger',
                                        'satici' => 'warning',
                                        'musteri' => 'primary'
                                    ][$kullanici->rol] ?? 'secondary';
                                    
                                    $rolAdi = [
                                        'yonetici' => 'Yönetici',
                                        'satici' => 'Satıcı',
                                        'musteri' => 'Müşteri'
                                    ][$kullanici->rol] ?? 'Bilinmiyor';
                                @endphp
                                <span class="badge bg-{{ $rolBadge }}">{{ $rolAdi }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Telefon:</strong></td>
                            <td>{{ $kullanici->telefon ?? 'Belirtilmemiş' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Adres:</strong></td>
                            <td>{{ $kullanici->adres ?? 'Belirtilmemiş' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Kayıt Tarihi:</strong></td>
                            <td>{{ $kullanici->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Son Güncelleme:</strong></td>
                            <td>{{ $kullanici->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-chart-bar"></i> İstatistikler</h6>
                </div>
                <div class="card-body">
                    @if($kullanici->rol === 'satici')
                        <div class="text-center mb-3">
                            <h4 class="text-primary">{{ $kullanici->magazalar->count() }}</h4>
                            <small>Mağaza Sayısı</small>
                        </div>
                        <div class="text-center mb-3">
                            <h4 class="text-success">{{ $kullanici->magazalar->sum(function($magaza) { return $magaza->urunler->count(); }) }}</h4>
                            <small>Toplam Ürün</small>
                        </div>
                    @endif
                    
                    @if($kullanici->rol === 'musteri')
                        <div class="text-center mb-3">
                            <h4 class="text-info">{{ $kullanici->siparisler->count() }}</h4>
                            <small>Sipariş Sayısı</small>
                        </div>
                        <div class="text-center mb-3">
                            <h4 class="text-success">{{ number_format($kullanici->siparisler->sum('toplam_tutar'), 2) }} ₺</h4>
                            <small>Toplam Alışveriş</small>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-cogs"></i> İşlemler</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.kullanici.duzenle', $kullanici->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Kullanıcıyı Düzenle
                        </a>
                        @if($kullanici->rol !== 'yonetici')
                            <form method="POST" action="{{ route('admin.kullanici.sil', $kullanici->id) }}" 
                                  onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Kullanıcıyı Sil
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
