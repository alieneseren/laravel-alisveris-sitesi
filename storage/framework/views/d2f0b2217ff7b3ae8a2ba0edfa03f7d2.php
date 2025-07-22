

<?php $__env->startSection('title', 'Ana Sayfa - Pazaryeri'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>Pazaryeri'ne Hoş Geldiniz!</h1>
            <p>Binlerce ürün, güvenli alışveriş ve hızlı teslimat</p>
            <div class="search-bar">
                <form action="<?php echo e(route('arama')); ?>" method="GET">
                    <input type="text" name="q" placeholder="Ürün, marka veya kategori arayın..." value="<?php echo e(request('q')); ?>">
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
                <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="category-item">
                        <a href="<?php echo e(route('kategori', $kategori->id)); ?>" class="category-link">
                            <?php echo e($kategori->kategori_adi); ?>

                        </a>
                        <?php if($kategori->altKategoriler->count() > 0): ?>
                            <ul class="sub-categories">
                                <?php $__currentLoopData = $kategori->altKategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $altKategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e(route('kategori', $altKategori->id)); ?>">
                                            <?php echo e($altKategori->kategori_adi); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <?php $__currentLoopData = $urunler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="product-card" data-category-id="<?php echo e($urun->kategori->id ?? ''); ?>">
                        <div class="product-image">
                            <?php if($urun->gorseller->count() > 0): ?>
                                <img src="<?php echo e(asset($urun->gorseller->first()->gorsel_url)); ?>" 
                                     alt="<?php echo e($urun->ad); ?>" loading="lazy">
                            <?php else: ?>
                                <img src="<?php echo e(asset('storage/uploads/urunler/default-product.jpg')); ?>" 
                                     alt="<?php echo e($urun->ad); ?>" loading="lazy">
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <h3 class="product-title"><?php echo e($urun->ad); ?></h3>
                            <p class="product-price">₺<?php echo e(number_format($urun->fiyat, 2)); ?></p>
                            <p class="product-description">
                                <?php echo e(Str::limit($urun->aciklama, 100)); ?>

                            </p>
                            <div class="product-meta">
                                <span class="store-name"><?php echo e($urun->magaza->magaza_adi); ?></span>
                                <span class="stock-info"><?php echo e($urun->stok); ?> adet</span>
                            </div>
                            <div class="product-actions">
                                <a href="<?php echo e(route('urun.detay', $urun->id)); ?>" class="btn-primary">
                                    İncele
                                </a>
                                <?php if($urun->stok > 0): ?>
                                    <button class="btn-outline" 
                                            onclick="addToCart(<?php echo e($urun->id); ?>)">
                                        Sepete Ekle
                                    </button>
                                <?php else: ?>
                                    <button class="btn-secondary" disabled>
                                        Stokta Yok
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <?php echo e($urunler->links()); ?>

            </div>
        </main>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/home.blade.php ENDPATH**/ ?>