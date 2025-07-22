

<?php $__env->startSection('title', 'Profilim'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-user"></i> Profilim</h4>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(Auth::check()): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Kişisel Bilgiler</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Ad Soyad:</strong></td>
                                        <td><?php echo e(Session::get('kullanici_adi', Auth::user()->ad)); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>E-posta:</strong></td>
                                        <td><?php echo e(Auth::user()->eposta); ?></td>
                                    </tr>
                                    <?php if(Auth::user()->telefon): ?>
                                    <tr>
                                        <td><strong>Telefon:</strong></td>
                                        <td><?php echo e(Auth::user()->telefon); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if(Auth::user()->adres): ?>
                                    <tr>
                                        <td><strong>Adres:</strong></td>
                                        <td><?php echo e(Auth::user()->adres); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td><strong>Rol:</strong></td>
                                        <td>
                                            <?php
                                                $rol = Session::get('kullanici_rol', Auth::user()->rol);
                                                $rolAdi = [
                                                    'musteri' => 'Müşteri',
                                                    'satici' => 'Satıcı',
                                                    'yonetici' => 'Yönetici'
                                                ][$rol] ?? 'Bilinmiyor';
                                            ?>
                                            <span class="badge bg-primary"><?php echo e($rolAdi); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Üyelik Tarihi:</strong></td>
                                        <td><?php echo e(Auth::user()->created_at->format('d.m.Y')); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Hesap İşlemleri</h5>
                                <div class="d-grid gap-2">
                                    <a href="<?php echo e(route('profil.duzenle')); ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i> Profili Düzenle
                                    </a>
                                    <a href="<?php echo e(route('sifre.degistir')); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-key"></i> Şifre Değiştir
                                    </a>
                                    <?php if(Session::get('kullanici_rol') === 'musteri'): ?>
                                        <a href="<?php echo e(route('siparislerim')); ?>" class="btn btn-outline-info">
                                            <i class="fas fa-shopping-bag"></i> Siparişlerim
                                        </a>
                                    <?php endif; ?>
                                    <?php if(Session::get('kullanici_rol') === 'satici'): ?>
                                        <a href="<?php echo e(route('satici.dashboard')); ?>" class="btn btn-outline-success">
                                            <i class="fas fa-store"></i> Satıcı Paneli
                                        </a>
                                        <a href="<?php echo e(route('satici.urunler')); ?>" class="btn btn-outline-warning">
                                            <i class="fas fa-box"></i> Ürünlerim
                                        </a>
                                    <?php endif; ?>
                                    <?php if(Session::get('kullanici_rol') === 'yonetici'): ?>
                                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline-danger">
                                            <i class="fas fa-cog"></i> Admin Panel
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <div class="col-12">
                                <h5>Hızlı İstatistikler</h5>
                                <div class="row text-center">
                                    <?php if(Session::get('kullanici_rol') === 'musteri'): ?>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-primary">0</h3>
                                                    <small class="text-muted">Toplam Sipariş</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-success">₺0</h3>
                                                    <small class="text-muted">Toplam Harcama</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-info">0</h3>
                                                    <small class="text-muted">Sepetimde</small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if(Session::get('kullanici_rol') === 'satici'): ?>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-primary">0</h3>
                                                    <small class="text-muted">Aktif Ürün</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-success">₺0</h3>
                                                    <small class="text-muted">Toplam Satış</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-warning">0</h3>
                                                    <small class="text-muted">Bekleyen Sipariş</small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if(Session::get('kullanici_rol') === 'yonetici'): ?>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-primary">0</h3>
                                                    <small class="text-muted">Toplam Kullanıcı</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-success">0</h3>
                                                    <small class="text-muted">Toplam Ürün</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-warning">0</h3>
                                                    <small class="text-muted">Toplam Sipariş</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h3 class="text-info">₺0</h3>
                                                    <small class="text-muted">Toplam Ciro</small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <h5>Profil sayfasına erişmek için giriş yapmanız gerekiyor.</h5>
                            <a href="<?php echo e(route('login')); ?>" class="btn btn-primary">Giriş Yap</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/profil.blade.php ENDPATH**/ ?>