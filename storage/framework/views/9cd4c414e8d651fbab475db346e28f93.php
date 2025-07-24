<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Kayıt Ol - Pazaryeri</title>
    
    <!-- Favicon ve İkonlar -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('images/logo.svg')); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    
    <link href="<?php echo e(asset('css/login.css')); ?>" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-illustration">
            <h2>Aramıza Katılın!</h2>
            <p>Pazaryeri'nde satıcı olarak binlerce müşteriye ulaşabilir veya müşteri olarak güvenli alışveriş yapabilirsiniz!</p>
        </div>
        
        <div class="login-form-container">
            <div class="login-form">
                <h1>Kayıt Ol</h1>
                <p>Hesabınızı oluşturun</p>

                <?php if($errors->any()): ?>
                    <div class="modern-alert danger">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e($error); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('register.post')); ?>" id="registerForm">
                    <?php echo csrf_field(); ?>
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

                    <div class="form-group">
                        <label for="rol">Hesap Türü</label>
                        <select class="form-control" id="rol" name="rol" required>
                            <option value="">Seçiniz</option>
                            <option value="musteri" <?php echo e(old('rol') == 'musteri' ? 'selected' : ''); ?>>Müşteri</option>
                            <option value="satici" <?php echo e(old('rol') == 'satici' ? 'selected' : ''); ?>>Satıcı</option>
                        </select>
                    </div>

                    <div class="email-verification-info" style="background: #e3f2fd; border: 1px solid #2196f3; border-radius: 5px; padding: 15px; margin: 15px 0; color: #1976d2;">
                        <strong>📧 E-posta Doğrulama</strong><br>
                        Kayıt olduktan sonra e-posta adresinize 6 haneli bir doğrulama kodu gönderilecektir. Hesabınızı aktifleştirmek için bu kodu girmeniz gerekecektir.
                    </div>

                    <button type="submit" class="login-btn">Kayıt Ol</button>
                </form>

                <div class="register-link">
                    Zaten hesabınız var mı? <a href="<?php echo e(route('login')); ?>">Giriş Yap</a>
                </div>

                <div class="guest-link">
                    <a href="<?php echo e(route('home')); ?>">Misafir olarak devam et</a>
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

        // Form gönderiminden önce şifreleri kontrol et
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const sifre = document.getElementById('sifre').value;
            const sifreConfirmation = document.getElementById('sifre_confirmation').value;
            
            console.log('Şifre:', sifre);
            console.log('Şifre Tekrar:', sifreConfirmation);
            
            if (sifre !== sifreConfirmation) {
                e.preventDefault();
                alert('Şifreler eşleşmiyor!');
                return false;
            }
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/auth/register.blade.php ENDPATH**/ ?>