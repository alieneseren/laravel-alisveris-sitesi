@extends('layouts.app')

@section('title', 'Ana Sayfa - Pazaryeri')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>Pazaryeri'ne Hoş Geldiniz!</h1>
            <p>Binlerce ürün, güvenli alışveriş ve hızlı teslimat</p>
            <div class="search-bar">
                <form action="{{ route('arama') }}" method="GET">
                    <input type="text" name="q" placeholder="Ürün, marka veya kategori arayın..." value="{{ request('q') }}">
                    <button type="submit" class="btn-primary">Ara</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Ana İçerik -->
    <div class="modern-layout-flex">
        <!-- Sidebar -->
        <aside class="modern-sidebar">
            <h3>Kategoriler</h3>
            <div class="categories-list">
                @foreach($kategoriler as $kategori)
                    <div class="category-item">
                        <a href="{{ route('kategori', $kategori->id) }}" class="category-link">
                            {{ $kategori->kategori_adi }}
                        </a>
                        @if($kategori->altKategoriler->count() > 0)
                            <ul class="sub-categories">
                                @foreach($kategori->altKategoriler as $altKategori)
                                    <li>
                                        <a href="{{ route('kategori', $altKategori->id) }}">
                                            {{ $altKategori->kategori_adi }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
        </aside>

        <!-- Ana İçerik -->
        <main class="main-content-area">
            <div class="section-header">
                <h2>Öne Çıkan Ürünler</h2>
                <p>En popüler ve en çok satılan ürünler</p>
            </div>

            <!-- Ürün Listesi -->
            <div class="products-grid" id="urunler-listesi">
                @foreach($urunler as $urun)
                    <div class="product-card" data-category-id="{{ $urun->kategori->id ?? '' }}">
                        <div class="product-image">
                            @if($urun->gorseller->count() > 0)
                                <img src="{{ asset($urun->gorseller->first()->gorsel_url) }}" 
                                     alt="{{ $urun->ad }}" loading="lazy">
                            @else
                                <img src="{{ asset('storage/uploads/urunler/default-product.jpg') }}" 
                                     alt="{{ $urun->ad }}" loading="lazy">
                            @endif
                        </div>
                        
                        <div class="product-info">
                            <h3 class="product-title">{{ $urun->ad }}</h3>
                            <p class="product-price">₺{{ number_format($urun->fiyat, 2) }}</p>
                            <p class="product-description">
                                {{ Str::limit($urun->aciklama, 100) }}
                            </p>
                            <div class="product-meta">
                                <span class="store-name">{{ $urun->magaza->magaza_adi }}</span>
                                <span class="stock-info">{{ $urun->stok }} adet</span>
                            </div>
                            <div class="product-actions">
                                <a href="{{ route('urun.detay', $urun->id) }}" class="btn-primary">
                                    İncele
                                </a>
                                @if($urun->stok > 0)
                                    <button class="btn-outline" 
                                            onclick="addToCart({{ $urun->id }})">
                                        Sepete Ekle
                                    </button>
                                @else
                                    <button class="btn-secondary" disabled>
                                        Stokta Yok
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $urunler->links() }}
            </div>
        </main>
    </div>
</div>

@push('styles')
<style>
.hero-section {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 4rem 2rem;
    text-align: center;
    border-radius: 18px;
    margin-bottom: 3rem;
}

.hero-content h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    font-weight: 800;
}

.hero-content p {
    font-size: 1.3rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.search-bar {
    max-width: 600px;
    margin: 0 auto;
    display: flex;
    gap: 1rem;
}

.search-bar input {
    flex: 1;
    padding: 1rem;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
}

.search-bar button {
    padding: 1rem 2rem;
    border: none;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.search-bar button:hover {
    background: rgba(255, 255, 255, 0.3);
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-header h2 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: var(--dark);
}

.section-header p {
    font-size: 1.2rem;
    color: #666;
}

.category-item {
    margin-bottom: 1rem;
}

.category-link {
    display: block;
    padding: 0.8rem;
    background: var(--light);
    border-radius: 8px;
    text-decoration: none;
    color: var(--dark);
    font-weight: 500;
    transition: all 0.3s;
}

.category-link:hover {
    background: var(--primary);
    color: white;
}

.sub-categories {
    margin-top: 0.5rem;
    padding-left: 1rem;
}

.sub-categories li {
    margin-bottom: 0.3rem;
}

.sub-categories a {
    display: block;
    padding: 0.5rem;
    color: #666;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s;
}

.sub-categories a:hover {
    background: var(--light);
    color: var(--primary);
}

.product-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
    font-size: 0.9rem;
    color: #666;
}

.store-name {
    font-weight: 500;
}

.stock-info {
    font-size: 0.8rem;
    color: #28a745;
}

.pagination-wrapper {
    margin-top: 3rem;
    text-align: center;
}

@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2rem;
    }
    
    .search-bar {
        flex-direction: column;
    }
    
    .product-actions {
        flex-direction: column;
    }
}
</style>

<script>
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
            alert('Ürün sepete eklendi!');
            // Sepet sayısını güncelle
            updateCartCount();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Sepet hatası:', error);
        alert('Sepete eklenirken bir hata oluştu.');
    });
}

function updateCartCount() {
    fetch('/sepet/count')
        .then(response => response.json())
        .then(data => {
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = data.count;
            }
        })
        .catch(error => console.error('Sepet sayısı güncellenemedi:', error));
}
</script>
@endpush
@endsection
