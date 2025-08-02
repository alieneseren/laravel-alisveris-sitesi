@extends('layouts.app')

@section('title', 'Profilim')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-user"></i> Profilim</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(Auth::check())
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Kişisel Bilgiler</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Ad Soyad:</strong></td>
                                        <td>{{ Session::get('kullanici_adi', Auth::user()->ad) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>E-posta:</strong></td>
                                        <td>{{ Auth::user()->eposta }}</td>
                                    </tr>
                                    @if(Auth::user()->telefon)
                                    <tr>
                                        <td><strong>Telefon:</strong></td>
                                        <td>{{ Auth::user()->telefon }}</td>
                                    </tr>
                                    @endif
                                    @if(Auth::user()->adres)
                                    <tr>
                                        <td><strong>Adres:</strong></td>
                                        <td>{{ Auth::user()->adres }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Rol:</strong></td>
                                        <td>
                                            @php
                                                $rol = Session::get('kullanici_rol', Auth::user()->rol);
                                                $rolAdi = [
                                                    'musteri' => 'Müşteri',
                                                    'satici' => 'Satıcı',
                                                    'yonetici' => 'Yönetici'
                                                ][$rol] ?? 'Bilinmiyor';
                                            @endphp
                                            <span class="badge bg-primary">{{ $rolAdi }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Üyelik Tarihi:</strong></td>
                                        <td>{{ Auth::user()->created_at->format('d.m.Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Hesap İşlemleri</h5>
                                <div class="d-grid gap-2">
                                    @if(Session::get('kullanici_rol') === 'musteri')
                                        <a href="{{ route('siparislerim') }}" class="btn btn-primary">
                                            <i class="fas fa-shopping-bag"></i> Siparişlerim
                                        </a>
                                    @endif
                                    <a href="{{ route('profil.duzenle') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i> Profili Düzenle
                                    </a>
                                    <a href="{{ route('sifre.degistir') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-key"></i> Şifre Değiştir
                                    </a>
                                    @if(Session::get('kullanici_rol') === 'satici')
                                        <a href="{{ route('satici.siparisler') }}" class="btn btn-primary">
                                            <i class="fas fa-shopping-bag"></i> Sipariş Geçmişim
                                        </a>
                                        <a href="{{ route('satici.dashboard') }}" class="btn btn-outline-success">
                                            <i class="fas fa-store"></i> Satıcı Paneli
                                        </a>
                                        <a href="{{ route('satici.urunler') }}" class="btn btn-outline-warning">
                                            <i class="fas fa-box"></i> Ürünlerim
                                        </a>
                                    @endif
                                    @if(Session::get('kullanici_rol') === 'yonetici')
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-danger">
                                            <i class="fas fa-cog"></i> Admin Panel
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <div class="col-12">
                                <h5>Hızlı İstatistikler</h5>
                                <div class="row text-center">
                                    @if(Session::get('kullanici_rol') === 'musteri')
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-primary">{{ $stats['toplam_siparis'] ?? 0 }}</h3>
                                                    <small class="text-muted">Toplam Sipariş</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-success">₺{{ number_format($stats['toplam_harcama'] ?? 0, 2) }}</h3>
                                                    <small class="text-muted">Toplam Harcama</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-info">{{ $stats['sepetdeki_urun'] ?? 0 }}</h3>
                                                    <small class="text-muted">Sepetimde</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(Session::get('kullanici_rol') === 'satici')
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-primary">{{ $stats['aktif_urun'] ?? 0 }}</h3>
                                                    <small class="text-muted">Aktif Ürün</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-success">₺{{ number_format($stats['toplam_satis'] ?? 0, 2) }}</h3>
                                                    <small class="text-muted">Toplam Satış</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-warning">{{ $stats['bekleyen_siparis'] ?? 0 }}</h3>
                                                    <small class="text-muted">Bekleyen Sipariş</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if(Session::get('kullanici_rol') === 'yonetici')
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-primary">0</h3>
                                                    <small class="text-muted">Toplam Kullanıcı</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-success">0</h3>
                                                    <small class="text-muted">Toplam Ürün</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-warning">0</h3>
                                                    <small class="text-muted">Toplam Sipariş</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-info">₺0</h3>
                                                    <small class="text-muted">Toplam Ciro</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        @if(Session::get('kullanici_rol') === 'musteri' && isset($stats['son_siparisler']) && $stats['son_siparisler']->count() > 0)
                            <hr class="my-4">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5><i class="fas fa-clock"></i> Son Siparişlerim</h5>
                                        <a href="{{ route('siparislerim') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> Tümünü Gör
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Sipariş No</th>
                                                    <th>Tarih</th>
                                                    <th>Ürün Sayısı</th>
                                                    <th>Toplam</th>
                                                    <th>Durum</th>
                                                    <th>Ödeme</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['son_siparisler'] as $siparis)
                                                <tr>
                                                    <td><strong>#{{ $siparis->id }}</strong></td>
                                                    <td>{{ $siparis->created_at->format('d.m.Y') }}</td>
                                                    <td>{{ $siparis->siparisUrunleri->count() }} ürün</td>
                                                    <td>₺{{ number_format($siparis->toplam_tutar, 2) }}</td>
                                                    <td>
                                                        @php
                                                            $durumRenk = [
                                                                'bekliyor' => 'warning',
                                                                'onaylandi' => 'success', 
                                                                'kargoda' => 'info',
                                                                'teslim_edildi' => 'success',
                                                                'iptal' => 'danger'
                                                            ][$siparis->durum] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $durumRenk }}">{{ ucfirst($siparis->durum) }}</span>
                                                    </td>
                                                    <td>
                                                        @if($siparis->payment_status)
                                                            @php
                                                                $odemeRenk = [
                                                                    'pending' => 'warning',
                                                                    'paid' => 'success',
                                                                    'failed' => 'danger',
                                                                    'cancelled' => 'secondary'
                                                                ][$siparis->payment_status] ?? 'secondary';
                                                                $odemeText = [
                                                                    'pending' => 'Bekliyor',
                                                                    'paid' => 'Ödendi',
                                                                    'failed' => 'Başarısız',
                                                                    'cancelled' => 'İptal'
                                                                ][$siparis->payment_status] ?? $siparis->payment_status;
                                                            @endphp
                                                            <span class="badge bg-{{ $odemeRenk }}">{{ $odemeText }}</span>
                                                        @else
                                                            <span class="badge bg-secondary">Belirtilmemiş</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center">
                            <h5>Profil sayfasına erişmek için giriş yapmanız gerekiyor.</h5>
                            <a href="{{ route('login') }}" class="btn btn-primary">Giriş Yap</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
