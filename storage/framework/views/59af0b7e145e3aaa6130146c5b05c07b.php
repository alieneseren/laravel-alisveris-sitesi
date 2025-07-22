

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="filter-sidebar">
                <h4>Kategoriler</h4>
                <div class="category-filter">
                    <?php $__currentLoopData = $kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-check">
                            <?php
                                $currentKategoriler = request('kategori') ? explode(',', request('kategori')) : [];
                                $isChecked = in_array((string)$kategori->id, $currentKategoriler);
                            ?>
                            <input class="form-check-input" type="checkbox" value="<?php echo e($kategori->id); ?>" 
                                   id="kategori<?php echo e($kategori->id); ?>"
                                   <?php echo e($isChecked ? 'checked' : ''); ?>

                                   onchange="onCategoryChange(this)">
                            <label class="form-check-label" for="kategori<?php echo e($kategori->id); ?>">
                                <?php echo e($kategori->ad); ?>

                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <h4 class="mt-4">Fiyat Aralığı</h4>
                <div class="price-filter">
                    <div class="row">
                        <div class="col-6">
                            <input type="number" class="form-control" id="minPrice" placeholder="Min" 
                                   value="<?php echo e(request('min_fiyat')); ?>">
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control" id="maxPrice" placeholder="Max" 
                                   value="<?php echo e(request('max_fiyat')); ?>">
                        </div>
                    </div>
                    <button class="btn btn-primary mt-2 w-100" onclick="filterProducts()">Filtrele</button>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Ürünler</h2>
                <div class="sort-dropdown">
                    <select class="form-select" id="sortBy" onchange="sortProducts()">
                        <option value="created_at" <?php echo e(request('sort') == 'created_at' ? 'selected' : ''); ?>>En Yeni</option>
                        <option value="fiyat_asc" <?php echo e(request('sort') == 'fiyat_asc' ? 'selected' : ''); ?>>Fiyat: Düşük - Yüksek</option>
                        <option value="fiyat_desc" <?php echo e(request('sort') == 'fiyat_desc' ? 'selected' : ''); ?>>Fiyat: Yüksek - Düşük</option>
                        <option value="rating" <?php echo e(request('sort') == 'rating' ? 'selected' : ''); ?>>En Popüler</option>
                    </select>
                </div>
            </div>
            
            <div class="row" id="products-grid">
                <?php $__currentLoopData = $urunler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 col-sm-6 col-12 mb-4">
                        <div class="product-card">
                            <div class="product-image">
                                <?php if($urun->anaGorsel): ?>
                                    <img src="<?php echo e(asset($urun->anaGorsel->gorsel_url)); ?>" 
                                         alt="<?php echo e($urun->ad); ?>" class="img-fluid">
                                <?php elseif($urun->gorseller->count() > 0): ?>
                                    <img src="<?php echo e(asset($urun->gorseller->first()->gorsel_url)); ?>" 
                                         alt="<?php echo e($urun->ad); ?>" class="img-fluid">
                                <?php else: ?>
                                    <img src="<?php echo e(asset('storage/uploads/urunler/default-product.jpg')); ?>" 
                                         alt="<?php echo e($urun->ad); ?>" class="img-fluid">
                                <?php endif; ?>
                            </div>
                            <div class="product-details">
                                <h5 class="product-title"><?php echo e($urun->ad); ?></h5>
                                <p class="product-description"><?php echo e(Str::limit($urun->aciklama, 100)); ?></p>
                                <div class="product-price">
                                    <span class="price"><?php echo e(number_format($urun->fiyat, 2)); ?> ₺</span>
                                    <?php if($urun->stok > 0): ?>
                                        <span class="stock-status in-stock">Stokta</span>
                                    <?php else: ?>
                                        <span class="stock-status out-of-stock">Tükendi</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-meta">
                                    <small class="text-muted"><?php echo e($urun->magaza->magaza_adi); ?></small>
                                    <div class="rating">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="far fa-star"></i>
                                        <?php endfor; ?>
                                        <span class="rating-count">(<?php echo e($urun->yorumlar->count()); ?>)</span>
                                    </div>
                                </div>
                                <div class="product-actions">
                                    <a href="<?php echo e(route('urun.detay', $urun->id)); ?>" 
                                       class="btn btn-primary btn-sm">İncele</a>
                                    <?php if($urun->stok > 0): ?>
                                        <button class="btn btn-success btn-sm" 
                                                onclick="addToCart(<?php echo e($urun->id); ?>)">
                                            <i class="fas fa-cart-plus"></i> Sepete Ekle
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <?php if($urunler->isEmpty()): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4>Ürün bulunamadı</h4>
                    <p class="text-muted">Arama kriterlerinizi değiştirip tekrar deneyin.</p>
                </div>
            <?php endif; ?>
            
            <div class="d-flex justify-content-center">
                <?php echo e($urunler->links()); ?>

            </div>
        </div>
    </div>
</div>

<script>
function onCategoryChange(checkbox) {
    // Kategori değiştiğinde otomatik filtrele
    setTimeout(filterProducts, 100);
}

function filterProducts() {
    const kategoriler = [];
    document.querySelectorAll('.category-filter input:checked').forEach(checkbox => {
        kategoriler.push(checkbox.value);
    });
    
    const minPrice = document.getElementById('minPrice').value;
    const maxPrice = document.getElementById('maxPrice').value;
    
    // Mevcut URL parametrelerini koru (sayfalama vb. için)
    const urlParams = new URLSearchParams(window.location.search);
    
    // VS Code browser parametrelerini koru
    const preserveParams = ['id', 'vscodeBrowserReqId'];
    preserveParams.forEach(param => {
        if (urlParams.has(param)) {
            // Bu parametreleri koruyacağız
        }
    });
    
    // Kategori parametresini güncelle
    if (kategoriler.length > 0) {
        urlParams.set('kategori', kategoriler.join(','));
    } else {
        urlParams.delete('kategori');
    }
    
    // Fiyat parametrelerini güncelle
    if (minPrice) {
        urlParams.set('min_fiyat', minPrice);
    } else {
        urlParams.delete('min_fiyat');
    }
    
    if (maxPrice) {
        urlParams.set('max_fiyat', maxPrice);
    } else {
        urlParams.delete('max_fiyat');
    }
    
    // Sayfa numarasını sıfırla (yeni filtrede 1. sayfaya git)
    urlParams.delete('page');
    
    // Yeni URL'ye git
    window.location.search = urlParams.toString();
}

function sortProducts() {
    const sortBy = document.getElementById('sortBy').value;
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', sortBy);
    urlParams.delete('page'); // Sıralama değiştiğinde 1. sayfaya git
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/urun/index.blade.php ENDPATH**/ ?>