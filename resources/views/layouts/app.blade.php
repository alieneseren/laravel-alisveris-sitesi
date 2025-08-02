<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Pazaryeri - Türkiye\'nin en güvenilir e-ticaret platformu. Binlerce ürün, hızlı teslimat, güvenli ödeme.')">
    <meta name="keywords" content="pazaryeri, e-ticaret, online alışveriş, güvenli ödeme">
    <meta name="author" content="Pazaryeri">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('title', 'Pazaryeri - Modern E-ticaret Platformu')">
    <meta property="og:description" content="@yield('description', 'Türkiye\'nin en güvenilir e-ticaret platformu')">
    <meta property="og:image" content="{{ asset('images/logo.svg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Pazaryeri - Modern E-ticaret Platformu')">
    <meta name="twitter:description" content="@yield('description', 'Türkiye\'nin en güvenilir e-ticaret platformu')">
    <meta name="twitter:image" content="{{ asset('images/logo.svg') }}">
    
    <title>@yield('title', 'Pazaryeri - Modern E-ticaret Platformu')</title>
    
    <!-- Favicon ve İkonlar -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="{{ route('home') }}" class="logo">
                Pazar<span>yeri</span>
            </a>

            <!-- Mobile hamburger button -->
            <button class="hamburger-menu" id="hamburgerBtn">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="nav-menu" id="navMenu">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Ana Sayfa</a>
                <a href="{{ route('urun.index') }}" class="nav-link">Ürünler</a>
                
                <div class="dropdown">
                    <a href="#" class="nav-link" id="kategoriDropdownBtn">Kategoriler</a>
                    <div class="dropdown-menu" id="kategoriDropdownMenu">
                        @foreach($kategoriler ?? [] as $kategori)
                            <div class="dropdown-submenu">
                                <a href="{{ route('kategori', $kategori->id) }}" class="ana-kategori">{{ $kategori->kategori_adi }}</a>
                                @if($kategori->altKategoriler->count() > 0)
                                    <div class="dropdown-menu-sub">
                                        @foreach($kategori->altKategoriler as $altKategori)
                                            <a href="{{ route('kategori', $altKategori->id) }}">{{ $altKategori->kategori_adi }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('sepet.index') }}" class="nav-link">
                    <i class="fas fa-shopping-cart"></i> Sepet <span id="cart-count" class="cart-count">0</span>
                </a>

                @auth
                    <div class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle" id="profilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> {{ Session::get('kullanici_adi', auth()->user()->ad) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profilDropdown">
                            <li><a class="dropdown-item" href="{{ route('profil') }}">
                                <i class="fas fa-user"></i> Profilim
                            </a></li>
                            @if(Session::get('kullanici_rol') === 'musteri')
                                <li><a class="dropdown-item" href="{{ route('siparislerim') }}">
                                    <i class="fas fa-shopping-bag"></i> Siparişlerim
                                </a></li>
                            @endif
                            @if(Session::get('kullanici_rol') === 'satici')
                                <li><a class="dropdown-item" href="{{ route('satici.siparisler') }}">
                                    <i class="fas fa-shopping-bag"></i> Sipariş Geçmişim
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('satici.dashboard') }}">
                                    <i class="fas fa-store"></i> Satıcı Paneli
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('satici.urunler') }}">
                                    <i class="fas fa-box"></i> Ürünlerim
                                </a></li>
                            @endif
                            @if(Session::get('kullanici_rol') === 'yonetici')
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-cog"></i> Admin Panel
                                </a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('profil.duzenle') }}">
                                <i class="fas fa-edit"></i> Profili Düzenle
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('sifre.degistir') }}">
                                <i class="fas fa-key"></i> Şifre Değiştir
                            </a></li>
                        </ul>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="nav-logout">
                        @csrf
                        <button type="submit" class="nav-link logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Çıkış
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i> Giriş Yap
                    </a>
                    <a href="{{ route('register') }}" class="nav-link">
                        <i class="fas fa-user-plus"></i> Kayıt Ol
                    </a>
                @endauth
            </nav>
        </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div class="container">
                <div class="modern-alert success">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container">
                <div class="modern-alert danger">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
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
                        <li><a href="{{ route('home') }}">Ana Sayfa</a></li>
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
                @php
                    $adminSayisi = \Illuminate\Support\Facades\Cache::remember('admin_count', 60, function () {
                        return \App\Models\Kullanici::where('rol', 'yonetici')->count();
                    });
                @endphp
                @if($adminSayisi == 0)
                    <p>&copy; 2025 <a href="{{ route('admin.register') }}" style="color: inherit; text-decoration: none;">Pazaryeri</a>. Tüm hakları saklıdır.</p>
                @else
                    <p>&copy; 2025 Pazaryeri. Tüm hakları saklıdır.</p>
                @endif
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    
    <!-- Gizli Admin Erişimi -->
    @php
        $adminSayisi = \Illuminate\Support\Facades\Cache::remember('admin_count', 60, function () {
            return \App\Models\Kullanici::where('rol', 'yonetici')->count();
        });
    @endphp
    @if($adminSayisi == 0)
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
                window.location.href = '{{ route("admin.register") }}';
            }
        });
    </script>
    @endif
    
    @auth
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
    @endauth
    
    @stack('scripts')
</body>
</html>
