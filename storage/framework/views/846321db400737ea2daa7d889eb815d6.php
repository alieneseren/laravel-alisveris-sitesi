

<?php $__env->startSection('title', 'Siparişlerim - Satıcı Paneli'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-shopping-bag"></i> Mağaza Siparişleri</h4>
                    <div>
                        <span class="badge bg-info me-2"><?php echo e($magaza->ad); ?></span>
                        <a href="<?php echo e(route('satici.dashboard')); ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Dashboard'a Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($siparisler->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Sipariş No</th>
                                        <th>Müşteri</th>
                                        <th>Tarih</th>
                                        <th>Ürünler (Sadece Mağazamdan)</th>
                                        <th>Toplam Tutar</th>
                                        <th>Durum</th>
                                        <th>Ödeme Durumu</th>
                                        <th>İletişim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $siparisler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $siparis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        // Bu mağazaya ait ürünleri filtrele
                                        $magazaUrunleri = $siparis->siparisUrunleri->filter(function($item) use ($magaza) {
                                            return $item->urun && $item->urun->magaza_id == $magaza->id;
                                        });
                                        $magazaToplam = $magazaUrunleri->sum(function($item) {
                                            return $item->adet * $item->birim_fiyat;
                                        });
                                    ?>
                                    
                                    <?php if($magazaUrunleri->count() > 0): ?>
                                    <tr>
                                        <td>
                                            <strong>#<?php echo e($siparis->id); ?></strong>
                                        </td>
                                        <td>
                                            <strong><?php echo e($siparis->kullanici->ad ?? 'Bilinmiyor'); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo e($siparis->kullanici->eposta ?? ''); ?></small>
                                        </td>
                                        <td>
                                            <?php echo e($siparis->created_at->format('d.m.Y H:i')); ?>

                                            <br>
                                            <small class="text-muted">
                                                <?php echo e($siparis->created_at->diffForHumans()); ?>

                                            </small>
                                        </td>
                                        <td>
                                            <?php $__currentLoopData = $magazaUrunleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="mb-1">
                                                    <strong><?php echo e($item->urun->ad ?? 'Ürün'); ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo e($item->adet); ?> adet × ₺<?php echo e(number_format($item->birim_fiyat, 2)); ?>

                                                    </small>
                                                </div>
                                                <?php if(!$loop->last): ?> <hr class="my-1"> <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </td>
                                        <td>
                                            <strong>₺<?php echo e(number_format($magazaToplam, 2)); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                (Toplam sipariş: ₺<?php echo e(number_format($siparis->toplam_tutar, 2)); ?>)
                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                                $durumRenk = [
                                                    'bekliyor' => 'warning',
                                                    'onaylandi' => 'success', 
                                                    'kargoda' => 'info',
                                                    'teslim_edildi' => 'success',
                                                    'iptal' => 'danger'
                                                ][$siparis->durum] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo e($durumRenk); ?>">
                                                <?php echo e(ucfirst($siparis->durum)); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php if($siparis->payment_status): ?>
                                                <?php
                                                    $odemeRenk = [
                                                        'pending' => 'warning',
                                                        'paid' => 'success',
                                                        'failed' => 'danger',
                                                        'cancelled' => 'secondary'
                                                    ][$siparis->payment_status] ?? 'secondary';
                                                    $odemeText = [
                                                        'pending' => 'Bekliyor',
                                                        'paid' => 'Ödendi',
                                                        'failed' => 'Başarısız',
                                                        'cancelled' => 'İptal'
                                                    ][$siparis->payment_status] ?? $siparis->payment_status;
                                                ?>
                                                <span class="badge bg-<?php echo e($odemeRenk); ?>"><?php echo e($odemeText); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Belirtilmemiş</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($siparis->teslimat_telefonu): ?>
                                                <small>
                                                    <i class="fas fa-phone"></i> 
                                                    <?php echo e($siparis->teslimat_telefonu); ?>

                                                </small>
                                                <br>
                                            <?php endif; ?>
                                            <?php if($siparis->teslimat_adresi): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <?php echo e(Str::limit($siparis->teslimat_adresi, 50)); ?>

                                                </small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            <?php echo e($siparisler->links()); ?>

                        </div>
                        
                        <!-- İstatistikler -->
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4><?php echo e($siparisler->total()); ?></h4>
                                        <small>Toplam Sipariş</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4><?php echo e($siparisler->where('payment_status', 'paid')->count()); ?></h4>
                                        <small>Ödenen Sipariş</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4><?php echo e($siparisler->where('durum', 'bekliyor')->count()); ?></h4>
                                        <small>Bekleyen Sipariş</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <?php
                                            $toplamKazanc = 0;
                                            foreach($siparisler as $siparis) {
                                                $magazaUrunleri = $siparis->siparisUrunleri->filter(function($item) use ($magaza) {
                                                    return $item->urun && $item->urun->magaza_id == $magaza->id;
                                                });
                                                $toplamKazanc += $magazaUrunleri->sum(function($item) {
                                                    return $item->adet * $item->birim_fiyat;
                                                });
                                            }
                                        ?>
                                        <h4>₺<?php echo e(number_format($toplamKazanc, 2)); ?></h4>
                                        <small>Toplam Kazanç</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Henüz hiç sipariş bulunmuyor</h5>
                            <p class="text-muted">Ürünleriniz sipariş edildiğinde buradan takip edebilirsiniz.</p>
                            <a href="<?php echo e(route('satici.urunler')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ürün Ekle
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/satici/siparisler.blade.php ENDPATH**/ ?>