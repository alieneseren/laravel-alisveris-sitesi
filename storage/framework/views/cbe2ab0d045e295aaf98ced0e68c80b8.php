

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="product-gallery">
                <div class="main-image">
                    <?php if($urun->anaGorsel): ?>
                        <img src="<?php echo e(asset($urun->anaGorsel->gorsel_url)); ?>" 
                             alt="<?php echo e($urun->ad); ?>" class="img-fluid" id="mainImage">
                    <?php elseif($urun->gorseller->count() > 0): ?>
                        <img src="<?php echo e(asset($urun->gorseller->first()->gorsel_url)); ?>" 
                             alt="<?php echo e($urun->ad); ?>" class="img-fluid" id="mainImage">
                    <?php else: ?>
                        <img src="<?php echo e(asset('storage/uploads/urunler/default-product.jpg')); ?>" 
                             alt="<?php echo e($urun->ad); ?>" class="img-fluid" id="mainImage">
                    <?php endif; ?>
                </div>
                
                <?php if($urun->gorseller->count() > 1): ?>
                    <div class="thumbnail-images mt-3">
                        <div class="row">
                            <?php $__currentLoopData = $urun->gorseller; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $gorsel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-3">
                                    <img src="<?php echo e(asset($gorsel->gorsel_url)); ?>" 
                                         alt="<?php echo e($urun->ad); ?>" class="img-fluid thumbnail-img"
                                         onclick="changeMainImage('<?php echo e(asset($gorsel->gorsel_url)); ?>')">
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="product-info">
                <h1><?php echo e($urun->ad); ?></h1>
                
                <div class="product-rating mb-3">
                    <div class="rating">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="far fa-star"></i>
                        <?php endfor; ?>
                        <span class="rating-count">(<?php echo e($urun->yorumlar->count()); ?> değerlendirme)</span>
                    </div>
                </div>
                
                <div class="product-price mb-3">
                    <h3 class="price"><?php echo e(number_format($urun->fiyat, 2)); ?> ₺</h3>
                    <?php if($urun->stok > 0): ?>
                        <span class="stock-status in-stock">
                            <i class="fas fa-check-circle"></i> Stokta (<?php echo e($urun->stok); ?> adet)
                        </span>
                    <?php else: ?>
                        <span class="stock-status out-of-stock">
                            <i class="fas fa-times-circle"></i> Stokta yok
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="product-description mb-4">
                    <p><?php echo e($urun->aciklama); ?></p>
                </div>
                
                <div class="product-meta mb-4">
                    <div class="meta-item">
                        <strong>Kategori:</strong>
                        <?php $__currentLoopData = $urun->kategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('urun.index', ['kategori' => $kategori->id])); ?>" 
                               class="badge bg-secondary"><?php echo e($kategori->kategori_adi); ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="meta-item mt-2">
                        <strong>Satıcı:</strong>
                        <a href="<?php echo e(route('magaza.profil', $urun->magaza->id)); ?>"><?php echo e($urun->magaza->magaza_adi); ?></a>
                    </div>
                </div>
                
                <?php if($urun->stok > 0): ?>
                    <div class="product-actions mb-4">
                        <div class="quantity-selector mb-3">
                            <label for="quantity">Miktar:</label>
                            <div class="input-group" style="width: 150px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(-1)">-</button>
                                <input type="number" class="form-control text-center" id="quantity" 
                                       value="1" min="1" max="<?php echo e($urun->stok); ?>">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(1)">+</button>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <button class="btn btn-primary btn-lg me-2" onclick="addToCart()">
                                <i class="fas fa-cart-plus"></i> Sepete Ekle
                            </button>
                            <button class="btn btn-success btn-lg" onclick="buyNow()">
                                <i class="fas fa-bolt"></i> Hemen Al
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Bu ürün şu anda stokta bulunmamaktadır.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Ürün Detay Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" 
                            data-bs-target="#description" type="button" role="tab">
                        Ürün Açıklaması
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" 
                            data-bs-target="#reviews" type="button" role="tab">
                        Değerlendirmeler (<?php echo e($urun->yorumlar->count()); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="store-tab" data-bs-toggle="tab" 
                            data-bs-target="#store" type="button" role="tab">
                        Satıcı Bilgisi
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="productTabsContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="p-4">
                        <h4>Ürün Açıklaması</h4>
                        <p><?php echo e($urun->aciklama); ?></p>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="p-4">
                        <h4>Müşteri Değerlendirmeleri</h4>
                        
                        <?php if($urun->yorumlar->count() > 0): ?>
                            <?php $__currentLoopData = $urun->yorumlar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yorum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="review-item mb-4">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong><?php echo e($yorum->kullanici->ad); ?> <?php echo e($yorum->kullanici->soyad); ?></strong>
                                            <div class="rating">
                                                <?php for($i = 1; $i <= 5; $i++): ?>
                                                    <?php if($i <= $yorum->puan): ?>
                                                        <i class="fas fa-star"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <small class="text-muted"><?php echo e($yorum->created_at->format('d.m.Y')); ?></small>
                                    </div>
                                    <p class="mt-2"><?php echo e($yorum->yorum); ?></p>
                                    <hr>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <p class="text-muted">Henüz değerlendirme yapılmamış.</p>
                        <?php endif; ?>
                        
                        <?php if(auth()->guard()->check()): ?>
                            <div class="add-review mt-4">
                                <h5>Değerlendirme Yap</h5>
                                <form id="reviewForm">
                                    <?php echo csrf_field(); ?>
                                    <div class="mb-3">
                                        <label for="rating">Puan:</label>
                                        <div class="rating-input">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <input type="radio" name="puan" value="<?php echo e($i); ?>" id="star<?php echo e($i); ?>">
                                                <label for="star<?php echo e($i); ?>"><i class="fas fa-star"></i></label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="comment">Yorum:</label>
                                        <textarea class="form-control" name="yorum" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Değerlendirme Gönder</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <a href="<?php echo e(route('login')); ?>">Giriş yapın</a> ve bu ürün hakkında değerlendirme yapın.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="store" role="tabpanel">
                    <div class="p-4">
                        <h4><?php echo e($urun->magaza->ad); ?></h4>
                        <p><?php echo e($urun->magaza->aciklama); ?></p>
                        <p><strong>Adres:</strong> <?php echo e($urun->magaza->adres); ?></p>
                        <p><strong>Telefon:</strong> <?php echo e($urun->magaza->telefon); ?></p>
                        <a href="<?php echo e(route('magaza.profil', $urun->magaza->id)); ?>" class="btn btn-primary">
                            Mağaza Profilini Görüntüle
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
}

function changeQuantity(delta) {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    const newValue = currentValue + delta;
    const maxValue = parseInt(quantityInput.max);
    
    if (newValue >= 1 && newValue <= maxValue) {
        quantityInput.value = newValue;
    }
}

function addToCart() {
    <?php if(auth()->guard()->guest()): ?>
        // Misafir kullanıcı için login'e yönlendir
        if (confirm('Sepete ürün eklemek için giriş yapmanız gerekiyor. Giriş sayfasına yönlendirileceksiniz.')) {
            window.location.href = '<?php echo e(route("login")); ?>';
        }
        return;
    <?php endif; ?>
    
    const quantity = document.getElementById('quantity').value;
    
    fetch('/sepet/ekle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            urun_id: <?php echo e($urun->id); ?>,
            miktar: quantity
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
        console.error('Error:', error);
        alert('Bir hata oluştu!');
    });
}

function buyNow() {
    <?php if(auth()->guard()->guest()): ?>
        // Misafir kullanıcı için login'e yönlendir
        if (confirm('Hemen satın almak için giriş yapmanız gerekiyor. Giriş sayfasına yönlendirileceksiniz.')) {
            window.location.href = '<?php echo e(route("login")); ?>';
        }
        return;
    <?php endif; ?>
    
    addToCart();
    setTimeout(() => {
        window.location.href = '<?php echo e(route("sepet")); ?>';
    }, 1000);
}

function updateCartCount() {
    <?php if(auth()->guard()->check()): ?>
    fetch('/sepet/count')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge && data.count !== undefined) {
                cartBadge.textContent = data.count;
            }
        })
        .catch(error => {
            console.error('Cart count update error:', error);
            // Sessizce başarısız ol, kullanıcıya hata gösterme
        });
    <?php endif; ?>
}

// Değerlendirme formu
<?php if(auth()->guard()->check()): ?>
const reviewForm = document.getElementById('reviewForm');
if (reviewForm) {
    reviewForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/urun/<?php echo e($urun->id); ?>/yorum', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Değerlendirmeniz gönderildi!');
                location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Review submission error:', error);
        });
    });
}
<?php endif; ?>
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/urun/detay.blade.php ENDPATH**/ ?>