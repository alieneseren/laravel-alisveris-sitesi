@extends('layouts.app')

@section('title', 'Ürün Düzenle - ' . $urun->ad)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit"></i> Ürün Düzenle - {{ $urun->ad }}</h2>
                <div>
                    <a href="{{ route('admin.urun.goruntule', $urun->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> Detayları Görüntüle
                    </a>
                    <a href="{{ route('admin.urunler') }}" class="btn btn-outline-secondary">
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
                    <h5><i class="fas fa-edit"></i> Ürün Bilgilerini Düzenle</h5>
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

                    <form method="POST" action="{{ route('admin.urun.duzenle.post', $urun->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ad" class="form-label">Ürün Adı *</label>
                                    <input type="text" 
                                           class="form-control @error('ad') is-invalid @enderror" 
                                           id="ad" 
                                           name="ad" 
                                           value="{{ old('ad', $urun->ad) }}" 
                                           required>
                                    @error('ad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kategori_id" class="form-label">Kategori *</label>
                                    <select class="form-select @error('kategori_id') is-invalid @enderror" 
                                            id="kategori_id" 
                                            name="kategori_id" 
                                            required>
                                        <option value="">Kategori Seçin</option>
                                        @foreach($kategoriler as $kategori)
                                            <option value="{{ $kategori->id }}" 
                                                    {{ old('kategori_id', $urun->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                                {{ $kategori->ad }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="aciklama" class="form-label">Ürün Açıklaması</label>
                            <textarea class="form-control @error('aciklama') is-invalid @enderror" 
                                      id="aciklama" 
                                      name="aciklama" 
                                      rows="4" 
                                      placeholder="Ürün hakkında açıklama...">{{ old('aciklama', $urun->aciklama) }}</textarea>
                            @error('aciklama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maksimum 1000 karakter</div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fiyat" class="form-label">Fiyat (₺) *</label>
                                    <input type="number" 
                                           class="form-control @error('fiyat') is-invalid @enderror" 
                                           id="fiyat" 
                                           name="fiyat" 
                                           value="{{ old('fiyat', $urun->fiyat) }}" 
                                           min="0" 
                                           step="0.01" 
                                           required>
                                    @error('fiyat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stok" class="form-label">Stok Miktarı *</label>
                                    <input type="number" 
                                           class="form-control @error('stok') is-invalid @enderror" 
                                           id="stok" 
                                           name="stok" 
                                           value="{{ old('stok', $urun->stok) }}" 
                                           min="0" 
                                           required>
                                    @error('stok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="durum" class="form-label">Durum *</label>
                                    <select class="form-select @error('durum') is-invalid @enderror" 
                                            id="durum" 
                                            name="durum" 
                                            required>
                                        <option value="aktif" {{ old('durum', $urun->durum) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="pasif" {{ old('durum', $urun->durum) === 'pasif' ? 'selected' : '' }}>Pasif</option>
                                    </select>
                                    @error('durum')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Değişiklikleri Kaydet
                            </button>
                            <a href="{{ route('admin.urun.goruntule', $urun->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> İptal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Mevcut Ürün Bilgileri -->
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-info-circle"></i> Mevcut Bilgiler</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Mağaza:</strong></td>
                            <td>{{ $urun->magaza->ad ?? 'Mağaza yok' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Eklenme:</strong></td>
                            <td>{{ $urun->created_at->format('d.m.Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Son Güncelleme:</strong></td>
                            <td>{{ $urun->updated_at->format('d.m.Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Görsel Sayısı:</strong></td>
                            <td>{{ $urun->urunGorselleri->count() }} adet</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Ürün İstatistikleri -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-chart-bar"></i> Satış İstatistikleri</h6>
                </div>
                <div class="card-body">
                    @php
                        $toplam_satilan = $urun->siparisUrunleri->sum('miktar');
                        $toplam_kazanc = $toplam_satilan * $urun->fiyat;
                    @endphp
                    <div class="text-center">
                        <h4 class="text-primary">{{ $toplam_satilan }}</h4>
                        <small>Toplam Satış</small>
                        <hr>
                        <h5 class="text-success">{{ number_format($toplam_kazanc, 2) }} ₺</h5>
                        <small>Toplam Kazanç</small>
                    </div>
                </div>
            </div>

            <!-- Uyarılar -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-exclamation-triangle"></i> Uyarılar</h6>
                </div>
                <div class="card-body">
                    @if($urun->siparisUrunleri->count() > 0)
                        <div class="alert alert-warning alert-sm">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Bu ürün {{ $urun->siparisUrunleri->count() }} siparişte bulunduğu için silinebilir ancak dikkatli olun.
                            </small>
                        </div>
                    @endif
                    
                    @if($urun->stok <= 5)
                        <div class="alert alert-danger alert-sm">
                            <small>
                                <i class="fas fa-exclamation-triangle"></i>
                                Stok seviyesi kritik! Stok ekleyin.
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-tools"></i> Hızlı İşlemler</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.urun.goruntule', $urun->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Detayları Gör
                        </a>
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
        </div>
    </div>
</div>
@endsection
