@extends('layouts.app')

@section('title', 'Ürün Detayları - ' . $urun->ad)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-box"></i> {{ $urun->ad }} - Detaylar</h2>
                <div>
                    <a href="{{ route('admin.urun.duzenle', $urun->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Düzenle
                    </a>
                    <a href="{{ route('admin.urunler') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Ürün Bilgileri -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Ürün Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Ürün Adı:</strong></td>
                                    <td>{{ $urun->ad }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kategori:</strong></td>
                                    <td>
                                        @if($urun->kategori)
                                            <span class="badge bg-primary">{{ $urun->kategori->ad }}</span>
                                        @else
                                            <span class="text-muted">Kategori yok</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Mağaza:</strong></td>
                                    <td>
                                        @if($urun->magaza)
                                            <a href="{{ route('admin.magaza.goruntule', $urun->magaza->id) }}" class="text-decoration-none">
                                                {{ $urun->magaza->ad }}
                                            </a>
                                        @else
                                            <span class="text-muted">Mağaza yok</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Fiyat:</strong></td>
                                    <td><strong class="text-success">{{ number_format($urun->fiyat, 2) }} ₺</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Stok:</strong></td>
                                    <td>
                                        @if($urun->stok <= 5)
                                            <span class="badge bg-danger">{{ $urun->stok }} (Kritik)</span>
                                        @elseif($urun->stok <= 10)
                                            <span class="badge bg-warning">{{ $urun->stok }} (Az)</span>
                                        @else
                                            <span class="badge bg-success">{{ $urun->stok }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Durum:</strong></td>
                                    <td>
                                        @if($urun->durum === 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Pasif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Eklenme Tarihi:</strong></td>
                                    <td>{{ $urun->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Son Güncelleme:</strong></td>
                                    <td>{{ $urun->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($urun->aciklama)
                        <div class="mt-3">
                            <strong>Açıklama:</strong>
                            <p class="mt-2">{{ $urun->aciklama }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ürün Görselleri -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-images"></i> Ürün Görselleri ({{ $urun->urunGorselleri->count() }} adet)</h5>
                </div>
                <div class="card-body">
                    @if($urun->urunGorselleri->isNotEmpty())
                        <div class="row">
                            @foreach($urun->urunGorselleri as $gorsel)
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <img src="{{ asset('uploads/' . $gorsel->gorsel_url) }}" 
                                             alt="{{ $urun->ad }}" 
                                             class="card-img-top"
                                             style="height: 200px; object-fit: cover;">
                                        <div class="card-body p-2 text-center">
                                            @if($gorsel->ana_gorsel)
                                                <small class="badge bg-primary">Ana Görsel</small>
                                            @else
                                                <small class="text-muted">Ek Görsel</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-images fa-3x text-muted mb-3"></i>
                            <h5>Henüz görsel yüklenmemiş</h5>
                            <p class="text-muted">Bu ürün için henüz görsel bulunmuyor.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Yan Panel -->
        <div class="col-md-4">
            <!-- İstatistikler -->
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-chart-bar"></i> Ürün İstatistikleri</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @php
                            $toplam_satilan = $urun->siparisUrunleri->sum('miktar');
                            $toplam_kazanc = $toplam_satilan * $urun->fiyat;
                        @endphp
                        <h4 class="text-primary">{{ $toplam_satilan }}</h4>
                        <small>Toplam Satış</small>
                    </div>
                    <hr>
                    <div class="text-center mb-3">
                        <h5 class="text-success">{{ number_format($toplam_kazanc, 2) }} ₺</h5>
                        <small>Toplam Kazanç</small>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h5 class="text-info">{{ number_format($urun->fiyat * $urun->stok, 2) }} ₺</h5>
                        <small>Stok Değeri</small>
                    </div>
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-tools"></i> Hızlı İşlemler</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.urun.duzenle', $urun->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Ürünü Düzenle
                        </a>
                        
                        @if($urun->durum === 'aktif')
                            <form method="POST" action="{{ route('admin.urun.duzenle.post', $urun->id) }}" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="ad" value="{{ $urun->ad }}">
                                <input type="hidden" name="aciklama" value="{{ $urun->aciklama }}">
                                <input type="hidden" name="fiyat" value="{{ $urun->fiyat }}">
                                <input type="hidden" name="stok" value="{{ $urun->stok }}">
                                <input type="hidden" name="kategori_id" value="{{ $urun->kategori_id }}">
                                <input type="hidden" name="durum" value="pasif">
                                <button type="submit" class="btn btn-secondary btn-sm w-100">
                                    <i class="fas fa-pause"></i> Pasif Yap
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.urun.duzenle.post', $urun->id) }}" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="ad" value="{{ $urun->ad }}">
                                <input type="hidden" name="aciklama" value="{{ $urun->aciklama }}">
                                <input type="hidden" name="fiyat" value="{{ $urun->fiyat }}">
                                <input type="hidden" name="stok" value="{{ $urun->stok }}">
                                <input type="hidden" name="kategori_id" value="{{ $urun->kategori_id }}">
                                <input type="hidden" name="durum" value="aktif">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-play"></i> Aktif Yap
                                </button>
                            </form>
                        @endif
                        
                        <form method="POST" action="{{ route('admin.urun.sil', $urun->id) }}" onsubmit="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="fas fa-trash"></i> Ürünü Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Uyarılar -->
            @if($urun->stok <= 5 || $toplam_satilan == 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h6><i class="fas fa-exclamation-triangle"></i> Uyarılar</h6>
                    </div>
                    <div class="card-body">
                        @if($urun->stok <= 5)
                            <div class="alert alert-warning alert-sm mb-2">
                                <small>
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Stok seviyesi kritik! ({{ $urun->stok }} adet kaldı)
                                </small>
                            </div>
                        @endif
                        
                        @if($toplam_satilan == 0)
                            <div class="alert alert-info alert-sm">
                                <small>
                                    <i class="fas fa-info-circle"></i>
                                    Bu ürün henüz hiç satılmamış.
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
