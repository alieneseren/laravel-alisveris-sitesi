@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="admin-sidebar">
                <h4>Admin Panel</h4>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.kullanicilar') }}">
                            <i class="fas fa-users"></i> Kullanıcılar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.kategoriler') }}">
                            <i class="fas fa-tags"></i> Kategoriler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.urunler') }}">
                            <i class="fas fa-box"></i> Ürünler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.siparisler') }}">
                            <i class="fas fa-shopping-cart"></i> Siparişler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.magazalar') }}">
                            <i class="fas fa-store"></i> Mağazalar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.paythor.api') }}">
                            <i class="fas fa-credit-card"></i> Paythor API
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-10">
            <div class="admin-content">
                <h1>Admin Dashboard</h1>
                
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>{{ $istatistikler['toplam_kullanici'] }}</h4>
                                        <span>Toplam Kullanıcı</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>{{ $istatistikler['toplam_urun'] }}</h4>
                                        <span>Toplam Ürün</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-box fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>{{ $istatistikler['toplam_siparis'] }}</h4>
                                        <span>Toplam Sipariş</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-shopping-cart fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>{{ $istatistikler['toplam_magaza'] }}</h4>
                                        <span>Toplam Mağaza</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-store fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Paythor API Status -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        @php
                            $adminPaythorToken = Auth::user()->paythor_token;
                            $hasPaythorToken = !empty($adminPaythorToken);
                        @endphp
                        <div class="card {{ $hasPaythorToken ? 'border-success' : 'border-warning' }}">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card"></i> Paythor API Durumu
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        @if(!$hasPaythorToken)
                                            <div class="alert alert-warning mb-0">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Ödeme Sistemi Pasif!</strong> 
                                                Müşteriler ödeme yapabilmesi için Paythor API'sini bağlamanız gerekiyor.
                                            </div>
                                        @else
                                            <div class="alert alert-success mb-0">
                                                <i class="fas fa-check-circle"></i>
                                                <strong>Ödeme Sistemi Aktif!</strong> 
                                                Müşteriler güvenli ödeme yapabilir. Bağlı hesap: {{ session('paythor_email') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="{{ route('admin.paythor.api') }}" class="btn {{ $hasPaythorToken ? 'btn-success' : 'btn-warning' }}">
                                            <i class="fas fa-{{ $hasPaythorToken ? 'cog' : 'plug' }}"></i> 
                                            {{ $hasPaythorToken ? 'API Yönet' : 'API Bağla' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Son Siparişler</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Sipariş No</th>
                                                <th>Müşteri</th>
                                                <th>Tutar</th>
                                                <th>Durum</th>
                                                <th>Tarih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($son_siparisler as $siparis)
                                                <tr>
                                                    <td>#{{ $siparis->id }}</td>
                                                    <td>{{ $siparis->kullanici->ad }} {{ $siparis->kullanici->soyad }}</td>
                                                    <td>{{ number_format($siparis->toplam_tutar, 2) }} ₺</td>
                                                    <td>
                                                        <span class="badge bg-{{ $siparis->durum == 'tamamlandi' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($siparis->durum) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $siparis->created_at->format('d.m.Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Düşük Stoklu Ürünler</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Ürün</th>
                                                <th>Mağaza</th>
                                                <th>Stok</th>
                                                <th>Durum</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dusuk_stoklu_urunler as $urun)
                                                <tr>
                                                    <td>{{ Str::limit($urun->ad, 30) }}</td>
                                                    <td>{{ $urun->magaza->ad }}</td>
                                                    <td>{{ $urun->stok_miktari }}</td>
                                                    <td>
                                                        @if($urun->stok_miktari == 0)
                                                            <span class="badge bg-danger">Tükendi</span>
                                                        @else
                                                            <span class="badge bg-warning">Azalıyor</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
