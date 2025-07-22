

<?php $__env->startSection('title', 'Ürünlerim'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-box"></i> Ürünlerim</h2>
                <div>
                    <a href="<?php echo e(route('satici.dashboard')); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                    <a href="<?php echo e(route('satici.urun.ekle')); ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Yeni Ürün Ekle
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <strong><?php echo e($magaza->magaza_adi); ?></strong> mağazası - Toplam <?php echo e($urunler->count()); ?> ürün
                </div>
            </div>
        </div>
    </div>

    <?php if($urunler->isEmpty()): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h4>Henüz Ürününüz Yok</h4>
                        <p class="text-muted">İlk ürününüzü ekleyerek satışa başlayın!</p>
                        <a href="<?php echo e(route('satici.urun.ekle')); ?>" class="btn btn-success btn-lg">
                            <i class="fas fa-plus"></i> İlk Ürününüzü Ekleyin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <?php $__currentLoopData = $urunler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if($urun->gorseller->isNotEmpty()): ?>
                            <img src="<?php echo e(asset($urun->gorseller->first()->gorsel_url)); ?>" class="card-img-top" 
                                 style="height: 200px; object-fit: cover;" alt="<?php echo e($urun->ad); ?>">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo e($urun->ad); ?></h5>
                            <p class="card-text text-muted small">
                                <?php echo e(Str::limit($urun->aciklama, 100)); ?>

                            </p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 text-primary mb-0">₺<?php echo e(number_format($urun->fiyat, 2)); ?></span>
                                    <span class="badge bg-<?php echo e($urun->durum === 'aktif' ? 'success' : 'secondary'); ?>">
                                        <?php echo e($urun->durum === 'aktif' ? 'Aktif' : 'Pasif'); ?>

                                    </span>
                                </div>
                                <small class="text-muted">Stok: <?php echo e($urun->stok); ?> adet</small>
                                <?php if($urun->kategoriler->isNotEmpty()): ?>
                                    <div class="mt-2">
                                        <?php $__currentLoopData = $urun->kategoriler->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-light text-dark"><?php echo e($kategori->kategori_adi); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100" role="group">
                                <a href="<?php echo e(route('urun.detay', $urun->id)); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('satici.urun.duzenle', $urun->id)); ?>" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="<?php echo e(route('satici.urun.sil', $urun->id)); ?>" 
                                      style="display: inline;" onsubmit="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/satici/urunler.blade.php ENDPATH**/ ?>