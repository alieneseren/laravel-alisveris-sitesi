@extends('layouts.app')

@section('title', 'Mağaza Detayları - ' . $magaza->ad)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-store"></i> {{ $magaza->ad }} - Detaylar</h2>
                <div>
                    <a href="{{ route('admin.magaza.duzenle', $magaza->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Düzenle
                    </a>
                    <a href="{{ route('admin.magazalar') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mağaza Bilgileri -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Mağaza Bilgileri</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Mağaza Adı:</strong></td>
                            <td>{{ $magaza->ad }}</td>
                        </tr>
                        <tr>
                            <td><strong>Açıklama:</strong></td>
                            <td>{{ $magaza->aciklama ?? 'Açıklama yok' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Oluşturma Tarihi:</strong></td>
                            <td>{{ $magaza->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Son Güncelleme:</strong></td>
                            <td>{{ $magaza->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user"></i> Mağaza Sahibi</h5>
                </div>
                <div class="card-body">
                    @if($magaza->kullanici)
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Ad Soyad:</strong></td>
                                <td>{{ $magaza->kullanici->ad }}</td>
                            </tr>
                            <tr>
                                <td><strong>E-posta:</strong></td>
                                <td>{{ $magaza->kullanici->eposta }}</td>
                            </tr>
                            <tr>
                                <td><strong>Rol:</strong></td>
                                <td><span class="badge bg-warning">{{ ucfirst($magaza->kullanici->rol) }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Kayıt Tarihi:</strong></td>
                                <td>{{ $magaza->kullanici->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </table>
                    @else
                        <p class="text-muted">Mağaza sahibi bilgisi bulunamadı.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- İstatistikler -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>{{ $magaza->urunler->count() }}</h3>
                    <p>Toplam Ürün</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ $magaza->urunler->where('stok', '>', 0)->count() }}</h3>
                    <p>Stokta Olan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3>{{ $magaza->urunler->where('stok', 0)->count() }}</h3>
                    <p>Stok Yok</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    @php
                        $toplamDeger = $magaza->urunler->sum(function($urun) {
                            return $urun->fiyat * $urun->stok;
                        });
                    @endphp
                    <h3>{{ number_format($toplamDeger, 0) }} ₺</h3>
                    <p>Toplam Stok Değeri</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ürünler -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-boxes"></i> Mağaza Ürünleri ({{ $magaza->urunler->count() }} ürün)</h5>
                </div>
                <div class="card-body">
                    @if($magaza->urunler->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Görsel</th>
                                        <th>Ürün Adı</th>
                                        <th>Kategori</th>
                                        <th>Fiyat</th>
                                        <th>Stok</th>
                                        <th>Durum</th>
                                        <th>Eklenme Tarihi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($magaza->urunler as $urun)
                                        <tr>
                                            <td>
                                                @if($urun->urunGorselleri->first())
                                                    <img src="{{ asset('uploads/' . $urun->urunGorselleri->first()->gorsel_yolu) }}" 
                                                         alt="{{ $urun->ad }}" 
                                                         style="width: 40px; height: 40px; object-fit: cover;"
                                                         class="rounded">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $urun->ad }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($urun->aciklama, 50) }}</small>
                                            </td>
                                            <td>{{ $urun->kategori->ad ?? 'Kategori yok' }}</td>
                                            <td><strong>{{ number_format($urun->fiyat, 2) }} ₺</strong></td>
                                            <td>
                                                @if($urun->stok <= 5)
                                                    <span class="badge bg-danger">{{ $urun->stok }}</span>
                                                @elseif($urun->stok <= 10)
                                                    <span class="badge bg-warning">{{ $urun->stok }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ $urun->stok }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($urun->stok > 0)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Stok Yok</span>
                                                @endif
                                            </td>
                                            <td>{{ $urun->created_at->format('d.m.Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h5>Bu mağazada henüz ürün bulunmuyor</h5>
                            <p class="text-muted">Mağaza sahibi henüz ürün eklememiş.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
