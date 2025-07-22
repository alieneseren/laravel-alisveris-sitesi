@extends('layouts.app')

@section('title', 'Yeni Ürün Ekle')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-plus"></i> Yeni Ürün Ekle</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('satici.urun.ekle.post') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="urun_adi" class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('urun_adi') is-invalid @enderror" 
                                   id="urun_adi" name="urun_adi" value="{{ old('urun_adi') }}" required>
                            @error('urun_adi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="urun_aciklamasi" class="form-label">Ürün Açıklaması <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('urun_aciklamasi') is-invalid @enderror" 
                                      id="urun_aciklamasi" name="urun_aciklamasi" rows="4" required>{{ old('urun_aciklamasi') }}</textarea>
                            @error('urun_aciklamasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fiyat" class="form-label">Fiyat (₺) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('fiyat') is-invalid @enderror" 
                                           id="fiyat" name="fiyat" value="{{ old('fiyat') }}" required>
                                    @error('fiyat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stok" class="form-label">Stok Adedi <span class="text-danger">*</span></label>
                                    <input type="number" min="0" class="form-control @error('stok') is-invalid @enderror" 
                                           id="stok" name="stok" value="{{ old('stok') }}" required>
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
                                                           {{ in_array($kategori->id, old('kategoriler', [])) ? 'checked' : '' }}>
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
                                                                   {{ in_array($altKategori->id, old('kategoriler', [])) ? 'checked' : '' }}>
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

                        <!-- Yeni Kategori Ekleme -->
                        <div class="mb-3">
                            <div class="card border-success">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-plus-circle text-success"></i> Yeni Alt Kategori Ekle</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="ust_kategori_id" class="form-label">Ana Kategori Seçin</label>
                                            <select class="form-select @error('ust_kategori_id') is-invalid @enderror" 
                                                    id="ust_kategori_id" name="ust_kategori_id">
                                                <option value="">Ana kategori seçin...</option>
                                                @foreach($kategoriler as $kategori)
                                                    <option value="{{ $kategori->id }}" 
                                                            {{ old('ust_kategori_id') == $kategori->id ? 'selected' : '' }}>
                                                        {{ $kategori->kategori_adi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('ust_kategori_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="yeni_kategori" class="form-label">Yeni Alt Kategori Adı</label>
                                            <input type="text" class="form-control @error('yeni_kategori') is-invalid @enderror" 
                                                   id="yeni_kategori" name="yeni_kategori" value="{{ old('yeni_kategori') }}"
                                                   placeholder="Örn: Gaming Monitör">
                                            @error('yeni_kategori')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle"></i> Mevcut kategorilerde ürününüze uygun kategori yoksa yeni alt kategori oluşturabilirsiniz.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Görsel Yükleme Seçenekleri -->
                        <div class="mb-3">
                            <label class="form-label">Ürün Görselleri</label>
                            
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" id="gorselTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="dosya-tab" data-bs-toggle="tab" 
                                            data-bs-target="#dosya" type="button" role="tab">
                                        <i class="fas fa-upload"></i> Dosya Yükle
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="url-tab" data-bs-toggle="tab" 
                                            data-bs-target="#url" type="button" role="tab">
                                        <i class="fas fa-link"></i> URL ile Ekle
                                    </button>
                                </li>
                            </ul>
                            
                            <!-- Tab content -->
                            <div class="tab-content border border-top-0 p-3" id="gorselTabContent">
                                <div class="tab-pane fade show active" id="dosya" role="tabpanel">
                                    <input type="file" class="form-control @error('gorsel_dosyalari.*') is-invalid @enderror" 
                                           name="gorsel_dosyalari[]" multiple accept="image/*" id="gorsel_dosyalari">
                                    @error('gorsel_dosyalari.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Birden fazla görsel seçebilirsiniz. Desteklenen formatlar: JPG, PNG, GIF (Max: 2MB)
                                    </div>
                                    <div id="dosya_preview" class="mt-3 row"></div>
                                </div>
                                <div class="tab-pane fade" id="url" role="tabpanel">
                                    <textarea class="form-control @error('gorsel_urls') is-invalid @enderror" 
                                              id="gorsel_urls" name="gorsel_urls" rows="4" 
                                              placeholder="Her satıra bir görsel URL'si yazın...">{{ old('gorsel_urls') }}</textarea>
                                    @error('gorsel_urls')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Örnek: https://via.placeholder.com/600x400/007bff/ffffff?text=Ürün+Görseli
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-2">
                                <i class="fas fa-info-circle"></i>
                                <strong>Not:</strong> Hiç görsel eklemezseniz otomatik placeholder görsel eklenecektir.
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Bilgi:</strong> Ürününüz eklendikten sonra hemen satışa sunulacaktır.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('satici.urunler') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Ürünü Ekle
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
                            ${index === 0 ? '<br><span class="badge bg-primary">Ana Görsel</span>' : ''}
                        </div>
                    </div>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    });
});

// Kategori seçim yardımcısı
document.addEventListener('DOMContentLoaded', function() {
    const ustKategoriSelect = document.getElementById('ust_kategori_id');
    const yeniKategoriInput = document.getElementById('yeni_kategori');
    
    function toggleYeniKategori() {
        if (ustKategoriSelect.value) {
            yeniKategoriInput.removeAttribute('disabled');
            yeniKategoriInput.parentElement.parentElement.style.opacity = '1';
        } else {
            yeniKategoriInput.setAttribute('disabled', 'disabled');
            yeniKategoriInput.parentElement.parentElement.style.opacity = '0.6';
        }
    }
    
    ustKategoriSelect.addEventListener('change', toggleYeniKategori);
    toggleYeniKategori(); // İlk yükleme
});
</script>
@endsection
