@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="filter-sidebar">
                <h4>Arama Filtreleri</h4>
                
                <div class="search-box mb-3">
                    <form action="{{ route('arama') }}" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" 
                                   value="{{ request('q') }}" placeholder="Ürün ara...">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <h5>Kategoriler</h5>
                <div class="category-filter">
                    @foreach($kategoriler as $kategori)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="{{ $kategori->id }}" 
                                   id="kategori{{ $kategori->id }}"
                                   {{ request('kategori') == $kategori->id ? 'checked' : '' }}>
                            <label class="form-check-label" for="kategori{{ $kategori->id }}">
                                {{ $kategori->ad }}
                            </label>
                        </div>
                        @if($kategori->altKategoriler->count() > 0)
                            <div class="ms-3">
                                @foreach($kategori->altKategoriler as $altKat)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $altKat->id }}" 
                                               id="kategori{{ $altKat->id }}">
                                        <label class="form-check-label" for="kategori{{ $altKat->id }}">
                                            {{ $altKat->ad }}
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
                    <h2>Arama Sonuçları</h2>
                    @if(request('q'))
                        <p class="text-muted">"{{ request('q') }}" için {{ $urunler->total() }} sonuç bulundu</p>
                    @else
                        <p class="text-muted">{{ $urunler->total() }} ürün bulundu</p>
                    @endif
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
                            <h4>Arama sonucu bulunamadı</h4>
                            @if(request('q'))
                                <p class="text-muted">"{{ request('q') }}" için sonuç bulunamadı.</p>
                            @else
                                <p class="text-muted">Arama yapmak için yukarıdaki kutuya anahtar kelime girin.</p>
                            @endif
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
    const query = '{{ request("q") }}';
    
    let url = '{{ route("arama") }}?';
    
    if (query) {
        url += 'q=' + encodeURIComponent(query) + '&';
    }
    
    if (kategoriler.length > 0) {
        url += 'kategori=' + kategoriler.join(',') + '&';
    }
    
    if (minPrice) {
        url += 'min_fiyat=' + minPrice + '&';
    }
    
    if (maxPrice) {
        url += 'max_fiyat=' + maxPrice + '&';
    }
    
    window.location.href = url;
}

function sortProducts() {
    const sortBy = document.getElementById('sortBy').value;
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', sortBy);
    window.location.search = urlParams.toString();
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
