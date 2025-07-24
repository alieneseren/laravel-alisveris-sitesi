

<?php $__env->startSection('title', 'Paythor API Yönetimi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-2">
            <div class="admin-sidebar">
                <h4>Admin Panel</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('admin.dashboard')); ?>">
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
                            <i class="fas fa-boxes"></i> Ürünler
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
                        <a class="nav-link active" href="<?php echo e(route('admin.paythor.api')); ?>">
                            <i class="fas fa-credit-card"></i> Paythor API
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-10">
            <div class="admin-content">
                <h1><i class="fas fa-credit-card"></i> Paythor API Yönetimi</h1>
                
                <?php if(session('success')): ?>
                    <div class="alert alert-success">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>
                
                <?php if(session('error')): ?>
                    <div class="alert alert-danger">
                        <?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?>

                <?php if(session('info')): ?>
                    <div class="alert alert-info">
                        <?php echo e(session('info')); ?>

                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-api"></i> API Bağlantı Durumu</h5>
                            </div>
                            <div class="card-body">
                                <?php if(session('pending_paythor_email')): ?>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-mobile-alt"></i>
                                        <strong>OTP Doğrulama Gerekli!</strong> 
                                        <strong><?php echo e(session('pending_paythor_email')); ?></strong> adresine gönderilen kodu girin.
                                    </div>
                                    
                                    <h6>OTP Kodunu Girin:</h6>
                                    <form method="POST" action="<?php echo e(route('admin.paythor.api.otp')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <div class="mb-3">
                                            <label for="otp" class="form-label">OTP Kodu</label>
                                            <input type="text" 
                                                   class="form-control <?php $__errorArgs = ['otp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="otp" 
                                                   name="otp" 
                                                   maxlength="8"
                                                   required 
                                                   autocomplete="one-time-code"
                                                   placeholder="Örnek: 123456">
                                            <?php $__errorArgs = ['otp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check"></i> OTP ile Doğrula
                                            </button>
                                            <a href="<?php echo e(route('admin.paythor.api')); ?>" class="btn btn-secondary"
                                               onclick="event.preventDefault(); 
                                                        fetch('<?php echo e(route('admin.paythor.api.sil')); ?>', {method: 'DELETE', headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'}})
                                                        .then(() => location.reload());">
                                                <i class="fas fa-times"></i> İptal Et
                                            </a>
                                        </div>
                                    </form>
                                <?php elseif(empty($paythorToken)): ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>API Bağlı Değil!</strong> 
                                        Paythor API'si henüz bağlanmamış. Müşteriler ödeme yapabilmesi için API'yi bağlamanız gerekiyor.
                                    </div>
                                    
                                    <h6>Paythor API Bilgilerini Girin:</h6>
                                    <form method="POST" action="<?php echo e(route('admin.paythor.api.kaydet')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <div class="mb-3">
                                            <label for="paythor_email" class="form-label">Paythor E-posta</label>
                                            <input type="email" 
                                                   class="form-control <?php $__errorArgs = ['paythor_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="paythor_email" 
                                                   name="paythor_email" 
                                                   value="<?php echo e(old('paythor_email')); ?>" 
                                                   required>
                                            <?php $__errorArgs = ['paythor_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <div class="mb-3">
                                            <label for="paythor_password" class="form-label">Paythor Şifre</label>
                                            <input type="password" 
                                                   class="form-control <?php $__errorArgs = ['paythor_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="paythor_password" 
                                                   name="paythor_password" 
                                                   required>
                                            <?php $__errorArgs = ['paythor_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plug"></i> API'yi Bağla
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>API Başarıyla Bağlandı!</strong>
                                        Paythor API aktif ve müşteriler ödeme yapabilir.
                                    </div>
                                    
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Bağlı E-posta:</strong></td>
                                            <td><?php echo e($paythorEmail); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Token Durumu:</strong></td>
                                            <td><span class="badge bg-success">Aktif</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Token (İlk 20 karakter):</strong></td>
                                            <td><code><?php echo e(substr($paythorToken, 0, 20)); ?>...</code></td>
                                        </tr>
                                    </table>
                                    
                                    <div class="mt-3">
                                        <form method="POST" action="<?php echo e(route('admin.paythor.api.sil')); ?>" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('API bağlantısını kaldırmak istediğinizden emin misiniz? Müşteriler ödeme yapamayacak!')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-unlink"></i> API Bağlantısını Kaldır
                                            </button>
                                        </form>
                                        
                                        <button type="button" class="btn btn-warning" onclick="location.reload()">
                                            <i class="fas fa-sync"></i> Durumu Yenile
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-info-circle"></i> Bilgi</h6>
                            </div>
                            <div class="card-body">
                                <h6>Paythor API Nedir?</h6>
                                <p>Paythor, müşterilerinizin güvenli bir şekilde ödeme yapabilmesi için kullanılan ödeme gateway'idir.</p>
                                
                                <h6>Nasıl Çalışır?</h6>
                                <ul>
                                    <li>Admin olarak Paythor hesabınızla giriş yapın</li>
                                    <li>Sistem otomatik olarak API token'ı alır</li>
                                    <li>Müşteriler sepetlerinde ödeme yapabilir</li>
                                    <li>Ödemeler güvenli olarak işlenir</li>
                                </ul>
                                
                                <h6>Önemli Notlar:</h6>
                                <div class="alert alert-info">
                                    <small>
                                        • API bağlanmadığında müşteriler ödeme yapamaz<br>
                                        • Token güvenlik nedeniyle kısmen gizlenir<br>
                                        • API hesabınızın aktif olduğundan emin olun
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6><i class="fas fa-chart-line"></i> API İstatistikleri</h6>
                            </div>
                            <div class="card-body">
                                <?php if(!empty($paythorToken)): ?>
                                    <div class="text-center">
                                        <h4 class="text-success"><?php echo e(date('d.m.Y H:i')); ?></h4>
                                        <small>Son bağlantı zamanı</small>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h5 class="text-primary">Aktif</h5>
                                            <small>Durum</small>
                                        </div>
                                        <div class="col-6">
                                            <h5 class="text-info">Global</h5>
                                            <small>Kapsam</small>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted">
                                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                                        <p>API bağlandıktan sonra istatistikler görünecek</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/admin/paythor_api.blade.php ENDPATH**/ ?>