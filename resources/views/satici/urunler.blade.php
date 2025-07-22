@extends('layouts.app')

@section('title', 'Ürünlerim')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-box"></i> Ürünlerim</h2>
                <div>
                    <a href="{{ route('satici.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                    <a href="{{ route('satici.urun.ekle') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Yeni Ürün Ekle
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <strong>{{ $magaza->magaza_adi }}</strong> mağazası - Toplam {{ $urunler->count() }} ürün
                </div>
            </div>
        </div>
    </div>

    @if($urunler->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h4>Henüz Ürününüz Yok</h4>
                        <p class="text-muted">İlk ürününüzü ekleyerek satışa başlayın!</p>
                        <a href="{{ route('satici.urun.ekle') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-plus"></i> İlk Ürününüzü Ekleyin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($urunler as $urun)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($urun->gorseller->isNotEmpty())
                            <img src="{{ asset($urun->gorseller->first()->gorsel_url) }}" class="card-img-top" 
                                 style="height: 200px; object-fit: cover;" alt="{{ $urun->ad }}">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $urun->ad }}</h5>
                            <p class="card-text text-muted small">
                                {{ Str::limit($urun->aciklama, 100) }}
                            </p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 text-primary mb-0">₺{{ number_format($urun->fiyat, 2) }}</span>
                                    <span class="badge bg-{{ $urun->durum === 'aktif' ? 'success' : 'secondary' }}">
                                        {{ $urun->durum === 'aktif' ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </div>
                                <small class="text-muted">Stok: {{ $urun->stok }} adet</small>
                                @if($urun->kategoriler->isNotEmpty())
                                    <div class="mt-2">
                                        @foreach($urun->kategoriler->take(2) as $kategori)
                                            <span class="badge bg-light text-dark">{{ $kategori->kategori_adi }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('urun.detay', $urun->id) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('satici.urun.duzenle', $urun->id) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('satici.urun.sil', $urun->id) }}" 
                                      style="display: inline;" onsubmit="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
