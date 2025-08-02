

<?php $__env->startSection('title', 'Şifre Değiştir'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-key"></i> Şifre Değiştir</h4>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Güvenlik uyarısı:</strong> Şifrenizi değiştirdikten sonra tüm cihazlarda yeniden giriş yapmanız gerekebilir.
                    </div>

                    <form method="POST" action="<?php echo e(route('sifre.degistir.post')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="mb-3">
                            <label for="mevcut_sifre" class="form-label">Mevcut Şifre <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control <?php $__errorArgs = ['mevcut_sifre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="mevcut_sifre" name="mevcut_sifre" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('mevcut_sifre')">
                                    <i class="fas fa-eye" id="mevcut_sifre_icon"></i>
                                </button>
                                <?php $__errorArgs = ['mevcut_sifre'];
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
                        </div>

                        <div class="mb-3">
                            <label for="yeni_sifre" class="form-label">Yeni Şifre <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control <?php $__errorArgs = ['yeni_sifre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="yeni_sifre" name="yeni_sifre" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('yeni_sifre')">
                                    <i class="fas fa-eye" id="yeni_sifre_icon"></i>
                                </button>
                                <?php $__errorArgs = ['yeni_sifre'];
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
                            <small class="form-text text-muted">Şifreniz en az 6 karakter olmalıdır.</small>
                        </div>

                        <div class="mb-3">
                            <label for="yeni_sifre_confirmation" class="form-label">Yeni Şifre Tekrar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" 
                                       id="yeni_sifre_confirmation" name="yeni_sifre_confirmation" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('yeni_sifre_confirmation')">
                                    <i class="fas fa-eye" id="yeni_sifre_confirmation_icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('profil')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Şifreyi Değiştir
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/auth/sifre-degistir.blade.php ENDPATH**/ ?>