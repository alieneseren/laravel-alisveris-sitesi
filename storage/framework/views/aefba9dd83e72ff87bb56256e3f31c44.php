

<?php $__env->startSection('title', 'Siparişlerim'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-shopping-bag"></i> Siparişlerim</h4>
                    <a href="<?php echo e(route('profil')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Profile Dön
                    </a>
                </div>
                <div class="card-body">
                    <?php if($siparisler->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Sipariş No</th>
                                        <th>Tarih</th>
                                        <th>Ürünler</th>
                                        <th>Toplam Tutar</th>
                                        <th>Durum</th>
                                        <th>Ödeme Durumu</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $siparisler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $siparis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <strong>#<?php echo e($siparis->id); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo e($siparis->created_at->format('d.m.Y H:i')); ?>

                                            <br>
                                            <small class="text-muted">
                                                <?php echo e($siparis->created_at->diffForHumans()); ?>

                                            </small>
                                        </td>
                                        <td>
                                            <?php if($siparis->siparisUrunleri->count() > 0): ?>
                                                <?php $__currentLoopData = $siparis->siparisUrunleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="mb-1">
                                                        <strong><?php echo e($item->urun->ad ?? 'Ürün'); ?></strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            Adet: <?php echo e($item->adet); ?> × <?php echo e(number_format($item->birim_fiyat, 2)); ?> TL
                                                        </small>
                                                    </div>
                                                    <?php if(!$loop->last): ?> <hr class="my-1"> <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Ürün bilgisi yok</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong class="text-primary">
                                                <?php echo e(number_format($siparis->toplam_tutar, 2)); ?> TL
                                            </strong>
                                        </td>
                                        <td>
                                            <?php
                                                $durumRenk = [
                                                    'beklemede' => 'warning',
                                                    'onaylandi' => 'success',
                                                    'kargoda' => 'info',
                                                    'teslim_edildi' => 'primary',
                                                    'iptal' => 'danger'
                                                ][$siparis->durum] ?? 'secondary';
                                                
                                                $durumMetin = [
                                                    'beklemede' => 'Beklemede',
                                                    'onaylandi' => 'Onaylandı',
                                                    'kargoda' => 'Kargoda',
                                                    'teslim_edildi' => 'Teslim Edildi',
                                                    'iptal' => 'İptal'
                                                ][$siparis->durum] ?? ucfirst($siparis->durum);
                                            ?>
                                            <span class="badge bg-<?php echo e($durumRenk); ?>"><?php echo e($durumMetin); ?></span>
                                        </td>
                                        <td>
                                            <?php
                                                $odemeDurumRenk = [
                                                    'pending' => 'warning',
                                                    'completed' => 'success',
                                                    'failed' => 'danger'
                                                ][$siparis->payment_status] ?? 'secondary';
                                                
                                                $odemeDurumMetin = [
                                                    'pending' => 'Bekliyor',
                                                    'completed' => 'Tamamlandı',
                                                    'failed' => 'Başarısız'
                                                ][$siparis->payment_status] ?? ucfirst($siparis->payment_status);
                                            ?>
                                            <span class="badge bg-<?php echo e($odemeDurumRenk); ?>"><?php echo e($odemeDurumMetin); ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#siparisDetay<?php echo e($siparis->id); ?>">
                                                    <i class="fas fa-eye"></i> Detay
                                                </button>
                                                <?php if($siparis->payment_status == 'pending'): ?>
                                                <a href="<?php echo e(route('sepet.odeme')); ?>" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-credit-card"></i> Öde
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if($siparisler->hasPages()): ?>
                        <div class="d-flex justify-content-center mt-3">
                            <?php echo e($siparisler->links()); ?>

                        </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Henüz bir siparişiniz bulunmuyor</h5>
                            <p class="text-muted">Alışverişe başlamak için ürünleri incelemeye başlayın!</p>
                            <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Alışverişe Başla
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sipariş Detay Modallari -->
<?php $__currentLoopData = $siparisler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $siparis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="siparisDetay<?php echo e($siparis->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-receipt"></i> Sipariş Detayı #<?php echo e($siparis->id); ?>

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-info-circle"></i> Sipariş Bilgileri</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Sipariş No:</strong></td>
                                <td>#<?php echo e($siparis->id); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tarih:</strong></td>
                                <td><?php echo e($siparis->created_at->format('d.m.Y H:i')); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Durum:</strong></td>
                                <td>
                                    <?php
                                        $durumRenk = [
                                            'beklemede' => 'warning',
                                            'onaylandi' => 'success',
                                            'kargoda' => 'info',
                                            'teslim_edildi' => 'primary',
                                            'iptal' => 'danger'
                                        ][$siparis->durum] ?? 'secondary';
                                        
                                        $durumMetin = [
                                            'beklemede' => 'Beklemede',
                                            'onaylandi' => 'Onaylandı',
                                            'kargoda' => 'Kargoda',
                                            'teslim_edildi' => 'Teslim Edildi',
                                            'iptal' => 'İptal'
                                        ][$siparis->durum] ?? ucfirst($siparis->durum);
                                    ?>
                                    <span class="badge bg-<?php echo e($durumRenk); ?>"><?php echo e($durumMetin); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Ödeme Durumu:</strong></td>
                                <td>
                                    <?php
                                        $odemeDurumRenk = [
                                            'pending' => 'warning',
                                            'completed' => 'success',
                                            'failed' => 'danger'
                                        ][$siparis->payment_status] ?? 'secondary';
                                        
                                        $odemeDurumMetin = [
                                            'pending' => 'Bekliyor',
                                            'completed' => 'Tamamlandı',
                                            'failed' => 'Başarısız'
                                        ][$siparis->payment_status] ?? ucfirst($siparis->payment_status);
                                    ?>
                                    <span class="badge bg-<?php echo e($odemeDurumRenk); ?>"><?php echo e($odemeDurumMetin); ?></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-truck"></i> Teslimat Bilgileri</h6>
                        <div class="bg-light p-3 rounded">
                            <?php if($siparis->teslimat_adresi): ?>
                                <p class="mb-1"><strong>Adres:</strong></p>
                                <p class="mb-2"><?php echo e($siparis->teslimat_adresi); ?></p>
                            <?php endif; ?>
                            <?php if($siparis->teslimat_telefonu): ?>
                                <p class="mb-1"><strong>Telefon:</strong></p>
                                <p class="mb-0"><?php echo e($siparis->teslimat_telefonu); ?></p>
                            <?php endif; ?>
                            <?php if(!$siparis->teslimat_adresi && !$siparis->teslimat_telefonu): ?>
                                <p class="text-muted mb-0">Teslimat bilgisi yok</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <hr>

                <h6><i class="fas fa-list"></i> Sipariş Edilen Ürünler</h6>
                <?php if($siparis->siparisUrunleri->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Ürün</th>
                                    <th>Adet</th>
                                    <th>Birim Fiyat</th>
                                    <th>Toplam</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $siparis->siparisUrunleri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($item->urun->ad ?? 'Ürün'); ?></td>
                                    <td><?php echo e($item->adet); ?></td>
                                    <td><?php echo e(number_format($item->birim_fiyat, 2)); ?> TL</td>
                                    <td><strong><?php echo e(number_format($item->adet * $item->birim_fiyat, 2)); ?> TL</strong></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr class="table-warning">
                                    <td colspan="3"><strong>Genel Toplam</strong></td>
                                    <td><strong><?php echo e(number_format($siparis->toplam_tutar, 2)); ?> TL</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Ürün bilgisi bulunamadı.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                <?php if($siparis->payment_status == 'pending'): ?>
                <a href="<?php echo e(route('sepet.odeme')); ?>" class="btn btn-warning">
                    <i class="fas fa-credit-card"></i> Ödeme Yap
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/auth/siparislerim.blade.php ENDPATH**/ ?>