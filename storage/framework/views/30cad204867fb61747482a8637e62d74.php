

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="admin-sidebar">
                <h4>Admin Panel</h4>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo e(route('admin.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.kullanicilar')); ?>">
                            <i class="fas fa-users"></i> Kullanıcılar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.kategoriler')); ?>">
                            <i class="fas fa-tags"></i> Kategoriler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.urunler')); ?>">
                            <i class="fas fa-box"></i> Ürünler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.siparisler')); ?>">
                            <i class="fas fa-shopping-cart"></i> Siparişler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.magazalar')); ?>">
                            <i class="fas fa-store"></i> Mağazalar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.paythor.api')); ?>">
                            <i class="fas fa-credit-card"></i> Paythor API
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-10">
            <div class="admin-content">
                <h1>Admin Dashboard</h1>
                
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo e($istatistikler['toplam_kullanici']); ?></h4>
                                        <span>Toplam Kullanıcı</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo e($istatistikler['toplam_urun']); ?></h4>
                                        <span>Toplam Ürün</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-box fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo e($istatistikler['toplam_siparis']); ?></h4>
                                        <span>Toplam Sipariş</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-shopping-cart fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo e($istatistikler['toplam_magaza']); ?></h4>
                                        <span>Toplam Mağaza</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-store fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Paythor API Status -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <?php
                            $adminPaythorToken = Auth::user()->paythor_token;
                            $hasPaythorToken = !empty($adminPaythorToken);
                        ?>
                        <div class="card <?php echo e($hasPaythorToken ? 'border-success' : 'border-warning'); ?>">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card"></i> Paythor API Durumu
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <?php if(!$hasPaythorToken): ?>
                                            <div class="alert alert-warning mb-0">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Ödeme Sistemi Pasif!</strong> 
                                                Müşteriler ödeme yapabilmesi için Paythor API'sini bağlamanız gerekiyor.
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-success mb-0">
                                                <i class="fas fa-check-circle"></i>
                                                <strong>Ödeme Sistemi Aktif!</strong> 
                                                Müşteriler güvenli ödeme yapabilir. Bağlı hesap: <?php echo e(session('paythor_email')); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="<?php echo e(route('admin.paythor.api')); ?>" class="btn <?php echo e($hasPaythorToken ? 'btn-success' : 'btn-warning'); ?>">
                                            <i class="fas fa-<?php echo e($hasPaythorToken ? 'cog' : 'plug'); ?>"></i> 
                                            <?php echo e($hasPaythorToken ? 'API Yönet' : 'API Bağla'); ?>

                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Son Siparişler</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Sipariş No</th>
                                                <th>Müşteri</th>
                                                <th>Tutar</th>
                                                <th>Durum</th>
                                                <th>Tarih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $son_siparisler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $siparis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td>#<?php echo e($siparis->id); ?></td>
                                                    <td><?php echo e($siparis->kullanici->ad); ?> <?php echo e($siparis->kullanici->soyad); ?></td>
                                                    <td><?php echo e(number_format($siparis->toplam_tutar, 2)); ?> ₺</td>
                                                    <td>
                                                        <span class="badge bg-<?php echo e($siparis->durum == 'tamamlandi' ? 'success' : 'warning'); ?>">
                                                            <?php echo e(ucfirst($siparis->durum)); ?>

                                                        </span>
                                                    </td>
                                                    <td><?php echo e($siparis->created_at->format('d.m.Y')); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Düşük Stoklu Ürünler</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Ürün</th>
                                                <th>Mağaza</th>
                                                <th>Stok</th>
                                                <th>Durum</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $dusuk_stoklu_urunler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e(Str::limit($urun->ad, 30)); ?></td>
                                                    <td><?php echo e($urun->magaza->ad); ?></td>
                                                    <td><?php echo e($urun->stok_miktari); ?></td>
                                                    <td>
                                                        <?php if($urun->stok_miktari == 0): ?>
                                                            <span class="badge bg-danger">Tükendi</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">Azalıyor</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>