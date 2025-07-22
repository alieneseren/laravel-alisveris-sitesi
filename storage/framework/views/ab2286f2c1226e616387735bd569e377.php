<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="<?php echo $__env->yieldContent('description', 'Pazaryeri - Türkiye\'nin en güvenilir e-ticaret platformu. Binlerce ürün, hızlı teslimat, güvenli ödeme.'); ?>">
    <meta name="keywords" content="pazaryeri, e-ticaret, online alışveriş, güvenli ödeme">
    <meta name="author" content="Pazaryeri">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo $__env->yieldContent('title', 'Pazaryeri - Modern E-ticaret Platformu'); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('description', 'Türkiye\'nin en güvenilir e-ticaret platformu'); ?>">
    <meta property="og:image" content="<?php echo e(asset('images/logo.svg')); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $__env->yieldContent('title', 'Pazaryeri - Modern E-ticaret Platformu'); ?>">
    <meta name="twitter:description" content="<?php echo $__env->yieldContent('description', 'Türkiye\'nin en güvenilir e-ticaret platformu'); ?>">
    <meta name="twitter:image" content="<?php echo e(asset('images/logo.svg')); ?>">
    
    <title><?php echo $__env->yieldContent('title', 'Pazaryeri - Modern E-ticaret Platformu'); ?></title>
    
    <!-- Favicon ve İkonlar -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('images/logo.svg')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('images/apple-touch-icon.png')); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('images/favicon-32x32.png')); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('images/favicon-16x16.png')); ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="<?php echo e(route('home')); ?>" class="logo">
                Pazar<span>yeri</span>
            </a>

            <!-- Mobile hamburger button -->
            <button class="hamburger-menu" id="hamburgerBtn">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="nav-menu" id="navMenu">
                <a href="<?php echo e(route('home')); ?>" class="nav-link <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">Ana Sayfa</a>
                <a href="<?php echo e(route('urun.index')); ?>" class="nav-link">Ürünler</a>
                
                <div class="dropdown">
                    <a href="#" class="nav-link" id="kategoriDropdownBtn">Kategoriler</a>
                    <div class="dropdown-menu" id="kategoriDropdownMenu">
                        <?php $__currentLoopData = $kategoriler ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="dropdown-submenu">
                                <a href="<?php echo e(route('kategori', $kategori->id)); ?>" class="ana-kategori"><?php echo e($kategori->kategori_adi); ?></a>
                                <?php if($kategori->altKategoriler->count() > 0): ?>
                                    <div class="dropdown-menu-sub">
                                        <?php $__currentLoopData = $kategori->altKategoriler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $altKategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route('kategori', $altKategori->id)); ?>"><?php echo e($altKategori->kategori_adi); ?></a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <a href="<?php echo e(route('sepet')); ?>" class="nav-link">
                    <i class="fas fa-shopping-cart"></i> Sepet <span id="cart-count" class="cart-count">0</span>
                </a>

                <?php if(auth()->guard()->check()): ?>
                    <a href="/profil" class="nav-link">
                        <i class="fas fa-user"></i> Profil
                    </a>
                    <?php if(auth()->user()->rol == 'yonetici'): ?>
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-link">
                            <i class="fas fa-cog"></i> Admin Panel
                        </a>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="nav-logout">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="nav-link logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Çıkış
                        </button>
                    </form>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i> Giriş Yap
                    </a>
                    <a href="<?php echo e(route('register')); ?>" class="nav-link">
                        <i class="fas fa-user-plus"></i> Kayıt Ol
                    </a>
                <?php endif; ?>
            </nav>
        </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <?php if(session('success')): ?>
            <div class="container">
                <div class="modern-alert success">
                    <?php echo e(session('success')); ?>

                </div>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="container">
                <div class="modern-alert danger">
                    <?php echo e(session('error')); ?>

                </div>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Pazaryeri</h3>
                    <p>Modern e-ticaret deneyimi</p>
                </div>
                <div class="footer-section">
                    <h4>Hızlı Linkler</h4>
                    <ul>
                        <li><a href="<?php echo e(route('home')); ?>">Ana Sayfa</a></li>
                        <li><a href="#">Hakkımızda</a></li>
                        <li><a href="#">İletişim</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Müşteri Hizmetleri</h4>
                    <ul>
                        <li><a href="#">Yardım</a></li>
                        <li><a href="#">İade & Değişim</a></li>
                        <li><a href="#">Kargo Takip</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <?php
                    $adminSayisi = \Illuminate\Support\Facades\Cache::remember('admin_count', 60, function () {
                        return \App\Models\Kullanici::where('rol', 'yonetici')->count();
                    });
                ?>
                <?php if($adminSayisi == 0): ?>
                    <p>&copy; 2025 <a href="<?php echo e(route('admin.register')); ?>" style="color: inherit; text-decoration: none;">Pazaryeri</a>. Tüm hakları saklıdır.</p>
                <?php else: ?>
                    <p>&copy; 2025 Pazaryeri. Tüm hakları saklıdır.</p>
                <?php endif; ?>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo e(asset('js/main.js')); ?>"></script>
    
    <!-- Gizli Admin Erişimi -->
    <?php
        $adminSayisi = \Illuminate\Support\Facades\Cache::remember('admin_count', 60, function () {
            return \App\Models\Kullanici::where('rol', 'yonetici')->count();
        });
    ?>
    <?php if($adminSayisi == 0): ?>
    <script>
        let keySequence = [];
        const targetSequence = ['a', 'd', 'm', 'i', 'n'];
        
        document.addEventListener('keydown', function(e) {
            keySequence.push(e.key.toLowerCase());
            
            // Son 5 tuşa bak
            if (keySequence.length > 5) {
                keySequence.shift();
            }
            
            // Eğer "admin" yazıldıysa admin register sayfasına yönlendir
            if (keySequence.join('') === targetSequence.join('')) {
                window.location.href = '<?php echo e(route("admin.register")); ?>';
            }
        });
    </script>
    <?php endif; ?>
    
    <?php if(auth()->guard()->check()): ?>
    <script>
        // Sayfa yüklendiğinde sepet sayısını güncelle
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/sepet/count')
                .then(response => response.json())
                .then(data => {
                    const cartBadge = document.querySelector('.cart-count');
                    if (cartBadge) {
                        cartBadge.textContent = data.count || 0;
                    }
                })
                .catch(error => console.error('Cart count error:', error));
        });
    </script>
    <?php endif; ?>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Enes\Desktop\kafama göre projeler\laravel-php-pazaryeri\resources\views/layouts/app.blade.php ENDPATH**/ ?>