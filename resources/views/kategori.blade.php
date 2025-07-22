@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="filter-sidebar">
                <h4>{{ $kategori->kategori_adi }}</h4>
                <p class="text-muted">{{ $urunler->total() }} ürün bulundu</p>
                
                <h5 class="mt-4">Kategoriler</h5>
                <div class="category-filter">
                    @php
                        if (request('kategori')) {
                            $seciliKategoriler = explode(',', request('kategori'));
                        } else {
                            $seciliKategoriler = [$kategori->id];
                        }
                        $seciliKategoriler = array_map('strval', $seciliKategoriler);
                    @endphp
                    @foreach($kategoriler as $kat)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="{{ $kat->id }}" 
                                   id="kategori{{ $kat->id }}"
                                   {{ in_array(strval($kat->id), $seciliKategoriler) ? 'checked' : '' }}>
                            <label class="form-check-label" for="kategori{{ $kat->id }}">
                                {{ $kat->kategori_adi }}
                            </label>
                        </div>
                        @if($kat->altKategoriler->count() > 0)
                            <div class="ms-3">
                                @foreach($kat->altKategoriler as $altKat)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $altKat->id }}" 
                                               id="kategori{{ $altKat->id }}"
                                               {{ in_array(strval($altKat->id), $seciliKategoriler) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kategori{{ $altKat->id }}">
                                            {{ $altKat->kategori_adi }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
                
                <h5 class="mt-4">Fiyat Aralığı</h5>
                <div class="price-filter">
                    <div class="row">
                        <div class="col-6">
                            <input type="number" class="form-control" id="minPrice" placeholder="Min" 
                                   value="{{ request('min_fiyat') }}">
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control" id="maxPrice" placeholder="Max" 
                                   value="{{ request('max_fiyat') }}">
                        </div>
                    </div>
                    <button class="btn btn-primary mt-2 w-100" onclick="filterProducts()">Filtrele</button>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>{{ $kategori->kategori_adi }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Ana Sayfa</a></li>
                            @if($kategori->ustKategori)
                                <li class="breadcrumb-item">
                                    <a href="{{ route('kategori', $kategori->ustKategori->id) }}">
                                        {{ $kategori->ustKategori->kategori_adi }}
                                    </a>
                                </li>
                            @endif
                            <li class="breadcrumb-item active">{{ $kategori->kategori_adi }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="sort-dropdown">
                    <select class="form-select" id="sortBy" onchange="sortProducts()">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>En Yeni</option>
                        <option value="fiyat_asc" {{ request('sort') == 'fiyat_asc' ? 'selected' : '' }}>Fiyat: Düşük - Yüksek</option>
                        <option value="fiyat_desc" {{ request('sort') == 'fiyat_desc' ? 'selected' : '' }}>Fiyat: Yüksek - Düşük</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>En Popüler</option>
                    </select>
                </div>
            </div>
            
            <div class="row" id="products-grid">
                @forelse($urunler as $urun)
                    <div class="col-md-4 col-sm-6 col-12 mb-4">
                        <div class="product-card">
                            <div class="product-image">
                                @if($urun->gorseller && $urun->gorseller->count() > 0)
                                    <img src="{{ asset($urun->gorseller->first()->gorsel_url) }}" 
                                         alt="{{ $urun->ad }}" class="img-fluid">
                                @else
                                    <img src="{{ asset('storage/uploads/urunler/default-product.jpg') }}" 
                                         alt="{{ $urun->ad }}" class="img-fluid">
                                @endif
                            </div>
                            <div class="product-details">
                                <h5 class="product-title">{{ $urun->ad }}</h5>
                                <p class="product-description">{{ Str::limit($urun->aciklama, 100) }}</p>
                                <div class="product-price">
                                    <span class="price">{{ number_format($urun->fiyat, 2) }} ₺</span>
                                    @if($urun->stok > 0)
                                        <span class="stock-status in-stock">Stokta ({{ $urun->stok }})</span>
                                    @else
                                        <span class="stock-status out-of-stock">Tükendi</span>
                                    @endif
                                </div>
                                <div class="product-meta">
                                    <small class="text-muted">{{ $urun->magaza->magaza_adi }}</small>
                                    <div class="rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= ($urun->yorumlar->avg('puan') ?? 0))
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                        <span class="rating-count">({{ $urun->yorumlar->count() }})</span>
                                    </div>
                                </div>
                                <div class="product-actions">
                                    <a href="{{ route('urun.detay', $urun->id) }}" 
                                       class="btn btn-primary btn-sm">İncele</a>
                                    @if($urun->stok > 0)
                                        <button class="btn btn-success btn-sm" 
                                                onclick="addToCart({{ $urun->id }})">
                                            <i class="fas fa-cart-plus"></i> Sepete Ekle
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4>Bu kategoride ürün bulunamadı</h4>
                            <p class="text-muted">Diğer kategorileri kontrol edebilirsiniz.</p>
                            <a href="{{ route('urun.index') }}" class="btn btn-primary">Tüm Ürünleri Gör</a>
                        </div>
                    </div>
                @endforelse
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $urunler->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function filterProducts() {
    const kategoriler = [];
    document.querySelectorAll('.category-filter input:checked').forEach(checkbox => {
        kategoriler.push(checkbox.value);
    });
    
    const minPrice = document.getElementById('minPrice').value;
    const maxPrice = document.getElementById('maxPrice').value;
    const sortBy = document.getElementById('sortBy').value;
    
    // Eğer hiç kategori seçilmemişse ana kategoriye git
    if (kategoriler.length === 0) {
        window.location.href = '/kategori/{{ $kategori->id }}';
        return;
    }
    
    // Eğer sadece bir kategori seçiliyse ve hiç filtre yoksa, doğrudan o kategorinin sayfasına git
    if (kategoriler.length === 1 && !minPrice && !maxPrice && (!sortBy || sortBy === 'created_at')) {
        window.location.href = '/kategori/' + kategoriler[0];
        return;
    }
    
    // Birden fazla kategori veya filtreler varsa URL parametreleriyle git
    const urlParams = new URLSearchParams();
    
    // Kategorileri parametreye ekle
    if (kategoriler.length > 0) {
        urlParams.set('kategori', kategoriler.join(','));
    }
    
    if (minPrice) {
        urlParams.set('min_fiyat', minPrice);
    }
    
    if (maxPrice) {
        urlParams.set('max_fiyat', maxPrice);
    }
    
    if (sortBy && sortBy !== 'created_at') {
        urlParams.set('sort', sortBy);
    }
    
    // Birden fazla kategori varsa /urunler sayfasını kullan
    let url;
    if (kategoriler.length > 1) {
        url = '/urunler';
    } else {
        url = '/kategori/' + kategoriler[0];
    }
    
    if (urlParams.toString()) {
        url += '?' + urlParams.toString();
    }
    
    window.location.href = url;
}

function sortProducts() {
    const sortBy = document.getElementById('sortBy').value;
    const urlParams = new URLSearchParams(window.location.search);
    
    // Mevcut kategori filtrelerini al
    const kategoriler = [];
    document.querySelectorAll('.category-filter input:checked').forEach(checkbox => {
        kategoriler.push(checkbox.value);
    });
    
    const minPrice = document.getElementById('minPrice').value;
    const maxPrice = document.getElementById('maxPrice').value;
    
    // Eğer sadece bir kategori seçiliyse ve hiç filtre yoksa, doğrudan o kategorinin sayfasına git
    if (kategoriler.length === 1 && !minPrice && !maxPrice && (!sortBy || sortBy === 'created_at')) {
        window.location.href = '/kategori/' + kategoriler[0];
        return;
    }
    
    // URL parametrelerini ayarla
    if (sortBy && sortBy !== 'created_at') {
        urlParams.set('sort', sortBy);
    } else {
        urlParams.delete('sort');
    }
    
    // Base kategori ID'sini belirle
    let baseKategoriId = kategoriler.length === 1 ? kategoriler[0] : '{{ $kategori->id }}';
    
    // Birden fazla kategori varsa kategori parametresi ekle
    if (kategoriler.length > 1) {
        urlParams.set('kategori', kategoriler.join(','));
    } else {
        urlParams.delete('kategori');
    }
    
    let url = '/kategori/' + baseKategoriId;
    if (urlParams.toString()) {
        url += '?' + urlParams.toString();
    }
    
    window.location.href = url;
}

function addToCart(urunId) {
    fetch('/sepet/ekle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            urun_id: urunId,
            miktar: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Ürün sepete eklendi!', 'success');
        } else {
            showNotification('Hata: ' + data.message, 'error');
        }
    });
}
</script>
@endsection
