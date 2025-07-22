@extends('layouts.app')

@section('title', 'Ürün Düzenle')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-edit"></i> Ürün Düzenle: {{ $urun->urun_adi }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('satici.urun.duzenle', $urun->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="urun_adi" class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('urun_adi') is-invalid @enderror" 
                                   id="urun_adi" name="urun_adi" value="{{ old('urun_adi', $urun->urun_adi) }}" required>
                            @error('urun_adi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="urun_aciklamasi" class="form-label">Ürün Açıklaması <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('urun_aciklamasi') is-invalid @enderror" 
                                      id="urun_aciklamasi" name="urun_aciklamasi" rows="4" required>{{ old('urun_aciklamasi', $urun->urun_aciklamasi) }}</textarea>
                            @error('urun_aciklamasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fiyat" class="form-label">Fiyat (₺) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('fiyat') is-invalid @enderror" 
                                           id="fiyat" name="fiyat" value="{{ old('fiyat', $urun->fiyat) }}" required>
                                    @error('fiyat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stok" class="form-label">Stok Adedi <span class="text-danger">*</span></label>
                                    <input type="number" min="0" class="form-control @error('stok') is-invalid @enderror" 
                                           id="stok" name="stok" value="{{ old('stok', $urun->stok) }}" required>
                                    @error('stok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategoriler <span class="text-danger">*</span></label>
                            @error('kategoriler')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <div class="row">
                                @foreach($kategoriler as $kategori)
                                    <div class="col-md-6 mb-2">
                                        <div class="card">
                                            <div class="card-header py-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="kategoriler[]" value="{{ $kategori->id }}" 
                                                           id="kat_{{ $kategori->id }}"
                                                           {{ in_array($kategori->id, old('kategoriler', $urun->kategoriler->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="kat_{{ $kategori->id }}">
                                                        {{ $kategori->kategori_adi }}
                                                    </label>
                                                </div>
                                            </div>
                                            @if($kategori->altKategoriler->isNotEmpty())
                                                <div class="card-body py-2">
                                                    @foreach($kategori->altKategoriler as $altKategori)
                                                        <div class="form-check ms-3">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="kategoriler[]" value="{{ $altKategori->id }}" 
                                                                   id="kat_{{ $altKategori->id }}"
                                                                   {{ in_array($altKategori->id, old('kategoriler', $urun->kategoriler->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="kat_{{ $altKategori->id }}">
                                                                {{ $altKategori->kategori_adi }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Mevcut Görseller -->
                        @if($urun->gorseller->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label">Mevcut Görseller</label>
                                <div class="row">
                                    @foreach($urun->gorseller as $gorsel)
                                        <div class="col-md-3 mb-2">
                                            <div class="card">
                                                <img src="{{ asset($gorsel->gorsel_url) }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                                <div class="card-body p-2">
                                                    @if($gorsel->ana_gorsel)
                                                        <span class="badge bg-primary">Ana Görsel</span>
                                                    @endif
                                                    <div class="form-check mt-1">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="silinecek_gorseller[]" value="{{ $gorsel->id }}" 
                                                               id="sil_{{ $gorsel->id }}">
                                                        <label class="form-check-label text-danger" for="sil_{{ $gorsel->id }}">
                                                            <small>Sil</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Yeni Görsel Ekleme -->
                        <div class="mb-3">
                            <label class="form-label">Yeni Görseller Ekle</label>
                            <input type="file" class="form-control @error('gorsel_dosyalari.*') is-invalid @enderror" 
                                   name="gorsel_dosyalari[]" multiple accept="image/*" id="gorsel_dosyalari">
                            @error('gorsel_dosyalari.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Yeni görseller ekleyebilirsiniz. Desteklenen formatlar: JPG, PNG, GIF (Max: 2MB)
                            </div>
                            <div id="dosya_preview" class="mt-3 row"></div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Bilgi:</strong> Değişiklikler kaydedildikten sonra ürününüz güncellenecektir.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('satici.urunler') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Değişiklikleri Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dosya önizleme
document.getElementById('gorsel_dosyalari').addEventListener('change', function(e) {
    const preview = document.getElementById('dosya_preview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                col.innerHTML = `
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            <small class="text-muted">${file.name}</small>
                        </div>
                    </div>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection
