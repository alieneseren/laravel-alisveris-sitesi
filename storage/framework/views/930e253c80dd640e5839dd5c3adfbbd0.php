<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Gizli Admin Kayıt - Pazaryeri</title>
    
    <!-- Favicon ve İkonlar -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('images/logo.svg')); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    
    <link href="<?php echo e(asset('css/login.css')); ?>" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-illustration">
            <h2>⚠️ Gizli Alan</h2>
            <p>Bu alan yalnızca yetkili yöneticiler içindir. Gizli kodu bilmiyorsanız lütfen çıkış yapın.</p>
        </div>
        
        <div class="login-form-container">
            <div class="login-form">
                <h1>🔐 Admin Kayıt</h1>
                <p>Yönetici hesabı oluşturun</p>

                <?php if($errors->any()): ?>
                    <div class="modern-alert danger">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e($error); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('admin.register.post')); ?>">
                    <?php echo csrf_field(); ?>
                    
                    <div class="form-group">
                        <label for="secret_code">🔑 Gizli Kod</label>
                        <input type="password" class="form-control" id="secret_code" name="secret_code" 
                               placeholder="Gizli kodu giriniz" required>
                    </div>

                    <div class="form-group">
                        <label for="ad">Ad Soyad</label>
                        <input type="text" class="form-control" id="ad" name="ad" 
                               value="<?php echo e(old('ad')); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="eposta">E-posta</label>
                        <input type="email" class="form-control" id="eposta" name="eposta" 
                               value="<?php echo e(old('eposta')); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="sifre">Şifre</label>
                        <div style="position: relative;">
                            <input type="password" class="form-control" id="sifre" name="sifre" required>
                            <span class="password-toggle" onclick="togglePassword('sifre')">👁️</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sifre_confirmation">Şifre Tekrar</label>
                        <div style="position: relative;">
                            <input type="password" class="form-control" id="sifre_confirmation" name="sifre_confirmation" required>
                            <span class="password-toggle" onclick="togglePassword('sifre_confirmation')">👁️</span>
                        </div>
                    </div>

                    <button type="submit" class="login-btn">👑 Yönetici Hesabı Oluştur</button>
                </form>

                <div class="register-link">
                    <a href="<?php echo e(route('login')); ?>">🔙 Normal Girişe Dön</a>
                </div>

                <div class="guest-link">
                    <a href="<?php echo e(route('home')); ?>">🏠 Ana Sayfaya Dön</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = passwordField.nextElementSibling;
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordField.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/auth/admin-register.blade.php ENDPATH**/ ?>