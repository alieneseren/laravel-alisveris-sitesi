

<?php $__env->startSection('title', 'Mağaza Oluştur'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-store"></i> Yeni Mağaza Oluştur</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('satici.magaza.olustur.post')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label for="magaza_adi" class="form-label">Mağaza Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['magaza_adi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="magaza_adi" name="magaza_adi" value="<?php echo e(old('magaza_adi')); ?>" required>
                            <?php $__errorArgs = ['magaza_adi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-text">Müşterilerinizin göreceği mağaza adınız</div>
                        </div>

                        <div class="mb-3">
                            <label for="magaza_aciklamasi" class="form-label">Mağaza Açıklaması <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php $__errorArgs = ['magaza_aciklamasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="magaza_aciklamasi" name="magaza_aciklamasi" rows="4" required><?php echo e(old('magaza_aciklamasi')); ?></textarea>
                            <?php $__errorArgs = ['magaza_aciklamasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-text">Mağazanız hakkında kısa bir açıklama yazın</div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Bilgi:</strong> Mağazanızı oluşturduktan sonra ürün eklemeye başlayabilirsiniz.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo e(route('satici.dashboard')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mağaza Oluştur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/satici/magaza-olustur.blade.php ENDPATH**/ ?>