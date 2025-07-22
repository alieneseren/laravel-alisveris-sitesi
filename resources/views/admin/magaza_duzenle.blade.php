@extends('layouts.app')

@section('title', 'Mağaza Düzenle - ' . $magaza->ad)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit"></i> Mağaza Düzenle - {{ $magaza->ad }}</h2>
                <div>
                    <a href="{{ route('admin.magaza.goruntule', $magaza->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> Detayları Görüntüle
                    </a>
                    <a href="{{ route('admin.magazalar') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-edit"></i> Mağaza Bilgilerini Düzenle</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.magaza.duzenle.post', $magaza->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="ad" class="form-label">Mağaza Adı *</label>
                            <input type="text" 
                                   class="form-control @error('ad') is-invalid @enderror" 
                                   id="ad" 
                                   name="ad" 
                                   value="{{ old('ad', $magaza->ad) }}" 
                                   required>
                            @error('ad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="aciklama" class="form-label">Mağaza Açıklaması</label>
                            <textarea class="form-control @error('aciklama') is-invalid @enderror" 
                                      id="aciklama" 
                                      name="aciklama" 
                                      rows="4" 
                                      placeholder="Mağaza hakkında açıklama...">{{ old('aciklama', $magaza->aciklama) }}</textarea>
                            @error('aciklama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maksimum 1000 karakter</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Değişiklikleri Kaydet
                            </button>
                            <a href="{{ route('admin.magaza.goruntule', $magaza->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> İptal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Mağaza Sahibi Bilgileri -->
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-user"></i> Mağaza Sahibi</h6>
                </div>
                <div class="card-body">
                    @if($magaza->kullanici)
                        <div class="text-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                        </div>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Ad:</strong></td>
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
                        </table>
                    @else
                        <p class="text-muted text-center">Mağaza sahibi bilgisi bulunamadı.</p>
                    @endif
                </div>
            </div>

            <!-- Mağaza İstatistikleri -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-chart-bar"></i> Mağaza İstatistikleri</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary">{{ $magaza->urunler->count() }}</h4>
                                <small>Ürün</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $magaza->urunler->where('stok', '>', 0)->count() }}</h4>
                            <small>Stokta</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-12">
                            @php
                                $toplamDeger = $magaza->urunler->sum(function($urun) {
                                    return $urun->fiyat * $urun->stok;
                                });
                            @endphp
                            <h5 class="text-info">{{ number_format($toplamDeger, 0) }} ₺</h5>
                            <small>Toplam Stok Değeri</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uyarılar -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-exclamation-triangle"></i> Uyarılar</h6>
                </div>
                <div class="card-body">
                    @if($magaza->urunler->count() > 0)
                        <div class="alert alert-warning alert-sm">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Bu mağazada {{ $magaza->urunler->count() }} ürün bulunduğu için mağaza silinemez.
                            </small>
                        </div>
                    @else
                        <div class="alert alert-info alert-sm">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Bu mağazada henüz ürün bulunmuyor.
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
