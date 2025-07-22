<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Giriş Yap - Pazaryeri</title>
    
    <!-- Favicon ve İkonlar -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-illustration">
            <h2>Hoş Geldiniz!</h2>
            <p>Pazaryeri'nde binlerce ürün ve satıcı sizi bekliyor. Hemen giriş yapın ve alışverişe başlayın!</p>
        </div>
        
        <div class="login-form-container">
            <div class="login-form">
                <h1>Giriş Yap</h1>
                <p>Hesabınıza giriş yapın</p>

                @if ($errors->any())
                    <div class="modern-alert danger">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="form-group">
                        <label for="eposta">E-posta</label>
                        <input type="email" class="form-control" id="eposta" name="eposta" 
                               value="{{ old('eposta') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="sifre">Şifre</label>
                        <div style="position: relative;">
                            <input type="password" class="form-control" id="sifre" name="sifre" required>
                            <span class="password-toggle" onclick="togglePassword()">👁️</span>
                        </div>
                    </div>

                    <button type="submit" class="login-btn">Giriş Yap</button>
                </form>

                <div class="register-link">
                    Hesabınız yok mu? <a href="{{ route('register') }}">Kayıt Ol</a>
                </div>

                <div class="guest-link">
                    <a href="{{ route('home') }}">Misafir olarak devam et</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('sifre');
            const toggleIcon = document.querySelector('.password-toggle');
            
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
