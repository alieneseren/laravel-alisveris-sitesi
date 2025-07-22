@extends('layouts.app')

@section('title', 'Sepetim')

@section('content')
<div class="container mt-4">
    <div class="modern-box glassmorphism shadow-lg p-4 mb-4 mt-4 animate-fade-in">
        <h2 class="modern-title mb-4"><i class="fas fa-shopping-cart"></i> Sepetim</h2>
        
        @if(session('success'))
            <div class="modern-alert success mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="modern-alert warning mb-4">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('error'))
            <div class="modern-alert danger mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="modern-alert info mb-4">
                {{ session('info') }}
            </div>
        @endif

        {{-- Misafir Kullanıcı Uyarısı --}}
        @guest
            <div class="modern-alert info mb-4" style="text-align:center;">
                <i class="fas fa-info-circle"></i>
                <strong>Misafir Alışveriş!</strong> 
                Sepetinize ürün ekleyebilir ve satın alabilirsiniz. Ödeme yapmak için 
                <a href="{{ route('login') }}" class="text-primary">giriş yapmanız</a> veya 
                <a href="{{ route('register') }}" class="text-primary">üye olmanız</a> gerekiyor.
            </div>
        @endguest

        {{-- Global API Token Uyarısı --}}
        @if(!$odemeAktif)
            <div class="modern-alert warning mb-4" style="text-align:center;">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Ödeme Sistemi Pasif!</strong> 
                Müşteriler ödeme yapabilmesi için admin'in Paythor API'sini bağlaması gerekiyor.
            </div>
        @endif

        @if(count($sepetUranlari) == 0)
            <div class="modern-alert warning mb-4">Sepetinizde ürün yok.</div>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                <h4 class="mt-3">Sepetiniz Boş</h4>
                <p class="text-muted">Sepetinizde henüz hiç ürün bulunmuyor.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Alışverişe Başla
                </a>
            </div>
        @else
            {{-- Müşteri Bilgileri Formu --}}
            <form id="odeme-bilgileri-form">
                <h3 class="mb-2" style="font-size:1.15rem;font-weight:600;">Müşteri Bilgileri</h3>
                <div style="display:flex;gap:12px;margin-bottom:18px;flex-wrap:wrap;">
                    <input type="text" id="musteri-ad" name="ad" class="form-control" placeholder="Ad" required 
                           style="min-width:120px;" value="{{ auth()->user()->ad ?? '' }}">
                    <input type="text" id="musteri-soyad" name="soyad" class="form-control" placeholder="Soyad" required 
                           style="min-width:120px;" value="{{ auth()->user()->soyad ?? '' }}">
                    <input type="email" id="musteri-eposta" name="eposta" class="form-control" placeholder="E-posta" required 
                           style="min-width:180px;" value="{{ auth()->user()->email ?? '' }}">
                    <input type="text" id="musteri-telefon" name="telefon" class="form-control" placeholder="Telefon" required 
                           style="min-width:120px;" value="{{ auth()->user()->telefon ?? '' }}">
                </div>
            </form>

            {{-- Sepet Tablosu --}}
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Mağaza</th>
                        <th>Fiyat</th>
                        <th>Adet</th>
                        <th>Ara Toplam</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sepetUranlari as $item)
                    <tr data-urun-id="{{ $item['urun']->id }}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="product-image-placeholder bg-light rounded" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $item['urun']->ad }}</h6>
                                    <small class="text-muted">{{ Str::limit($item['urun']->aciklama ?? '', 50) }}</small>
                                    <br><small class="text-info">Stok: {{ $item['urun']->stok }} adet</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $item['urun']->magaza->magaza_adi ?? 'Mağaza' }}</span>
                        </td>
                        <td>{{ number_format($item['urun']->fiyat, 2) }} ₺</td>
                        <td>
                            <div class="adet-controls">
                                <button class="adet-btn adet-azalt mini-btn" data-urun-id="{{ $item['urun']->id }}" title="Azalt">
                                    <span class="azalt-ikon">-</span>
                                </button>
                                <span class="adet-value" style="display:inline-block;min-width:22px;text-align:center;font-weight:600;font-size:1rem;">{{ $item['miktar'] }}</span>
                                <button class="adet-btn adet-arttir mini-btn" 
                                        data-urun-id="{{ $item['urun']->id }}" 
                                        data-stok="{{ $item['urun']->stok }}"
                                        data-mevcut-miktar="{{ $item['miktar'] }}"
                                        title="Arttır"
                                        @if($item['miktar'] >= $item['urun']->stok) disabled @endif>+</button>
                            </div>
                            @if($item['miktar'] >= $item['urun']->stok)
                                <small class="text-warning">Maksimum stok</small>
                            @endif
                        </td>
                        <td>{{ number_format($item['ara_toplam'], 2) }} ₺</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;"><strong>Toplam:</strong></td>
                        <td><strong>{{ number_format($toplam, 2) }} ₺</strong></td>
                    </tr>
                </tfoot>
            </table>

            {{-- Ödeme Butonları --}}
            <div style="text-align:right;margin-top:2.5rem;">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Alışverişe Devam Et
                </a>
                <button id="devam-et-btn" class="btn-main" @if(!$odemeAktif) disabled @endif>
                    Devam Et
                </button>
                <button id="paythor-odeme-btn" class="btn-main" style="display:none;" disabled>
                    Ödeme Yap
                </button>
            </div>

            {{-- Adres ve Ek Bilgi Formu (Başta Gizli) --}}
            <div id="adres-form-kutusu" class="modern-box glassmorphism shadow-lg p-4 mb-4 animate-fade-in" style="display:none;max-width:600px;margin:32px auto 0 auto;">
                <h3 class="modern-title mb-3">Teslimat ve Fatura Bilgileri</h3>
                <form id="adres-bilgi-formu">
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <input type="text" id="adres-adres" name="adres" class="form-control" placeholder="Adres" required style="min-width:220px;flex:1;">
                        <input type="text" id="adres-il" name="il" class="form-control" placeholder="İl" required style="min-width:120px;">
                        <input type="text" id="adres-ilce" name="ilce" class="form-control" placeholder="İlçe" required style="min-width:120px;">
                    </div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:12px;">
                        <input type="text" id="adres-posta" name="posta_kodu" class="form-control" placeholder="Posta Kodu" style="min-width:100px;">
                        <input type="text" id="adres-aciklama" name="aciklama" class="form-control" placeholder="Ek Açıklama (isteğe bağlı)" style="min-width:180px;flex:1;">
                    </div>
                    <div style="text-align:right;margin-top:18px;">
                        <button type="button" id="adres-iptal-btn" class="btn-main" style="background:#bbb;color:#222;">İptal</button>
                        <button type="submit" id="adres-onayla-btn" class="btn-main" style="margin-left:12px;">Onayla ve Ödeme Adımına Geç</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<style>
.mini-btn {
    font-size: 0.92rem;
    padding: 0.13rem 0.38rem;
    border-radius: 16px;
    margin: 0 1px;
    background: #eee;
    color: #333;
    border: none;
    box-shadow: 0 1px 4px rgba(67,97,238,0.04);
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}

.mini-btn:hover {
    background: #e0e0e0;
}

.mini-btn.delete-hover {
    background: #ff3b3b !important;
    color: #fff !important;
}

.adet-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2px;
}

.modern-alert {
    padding: 1rem 1.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-left: 6px solid var(--primary);
    background: #f8f9fa;
    color: var(--dark);
    margin-bottom: 1.5rem;
    border-radius: 8px;
}

.modern-alert.danger { 
    border-left-color: var(--danger); 
    color: var(--danger); 
    background: #fff0f3; 
}

.modern-alert.warning { 
    border-left-color: var(--warning); 
    color: var(--warning); 
    background: #fff8e1; 
}

.modern-alert.success {
    border-left-color: var(--success);
    color: var(--success);
    background: #e6fffa;
}

.glassmorphism {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
// PHP değişkenlerini JS'ye aktar
var odemeAktif = @json($odemeAktif);

document.addEventListener('DOMContentLoaded', function() {
    // Adet arttır/azalt
    document.querySelectorAll('.adet-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var urunId = this.getAttribute('data-urun-id');
            var row = this.closest('tr');
            var span = row.querySelector('.adet-value');
            var adet = parseInt(span.textContent);
            
            if (this.classList.contains('adet-arttir')) {
                var stok = parseInt(this.getAttribute('data-stok'));
                var mevcutMiktar = parseInt(this.getAttribute('data-mevcut-miktar'));
                
                if (adet >= stok) {
                    alert(`Bu ürünü sepetinize daha fazla ekleyemezsiniz. Stok: ${stok} adet`);
                    return;
                }
                adet++;
            } else if (this.classList.contains('adet-azalt')) {
                adet--;
            }
            
            if (adet < 1) {
                // 0 olursa ürünü sepetten kaldır
                fetch('{{ route("sepet.cikar", ":id") }}'.replace(':id', urunId), {
                    method: 'DELETE',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.text())
                .then(data => {
                    location.reload();
                });
                return;
            }
            
            // Adet güncelleme (AJAX ile)
            fetch('{{ route("sepet.adet.guncelle") }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    urun_id: urunId,
                    adet: adet
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    span.textContent = data.adet;
                    
                    // Stok durumuna göre arttır butonunu güncelle
                    var arttirBtn = row.querySelector('.adet-arttir');
                    var stok = parseInt(arttirBtn.getAttribute('data-stok'));
                    arttirBtn.setAttribute('data-mevcut-miktar', data.adet);
                    
                    if (data.adet >= stok) {
                        arttirBtn.disabled = true;
                        // Maksimum stok uyarısını göster
                        var uyari = row.querySelector('.text-warning');
                        if (!uyari) {
                            var uyariElement = document.createElement('small');
                            uyariElement.className = 'text-warning';
                            uyariElement.textContent = 'Maksimum stok';
                            row.querySelector('.adet-controls').parentNode.appendChild(uyariElement);
                        }
                    } else {
                        arttirBtn.disabled = false;
                        // Uyarıyı kaldır
                        var uyari = row.querySelector('.text-warning');
                        if (uyari) uyari.remove();
                    }
                    
                    // Ara toplam ve genel toplamı güncelle
                    var araTotalamCell = row.querySelector('td:nth-child(6)'); // 6. kolona güncellendi (stok bilgisi eklendi)
                    if (araTotalamCell) {
                        araTotalamCell.textContent = data.ara_toplam + ' ₺';
                    }
                    // Genel toplamı güncelle
                    var genelToplamCell = document.querySelector('tfoot strong');
                    if (genelToplamCell) {
                        genelToplamCell.textContent = data.toplam + ' ₺';
                    }
                } else {
                    alert(data.message || 'Adet güncellenemedi.');
                }
            })
            .catch(() => {
                alert('Bağlantı hatası.');
            });
        });
    });

    // Adres/bilgi adımı ve ödeme adımı yönetimi
    var devamEtBtn = document.getElementById('devam-et-btn');
    var odemeBtn = document.getElementById('paythor-odeme-btn');
    var adresFormKutusu = document.getElementById('adres-form-kutusu');
    var adresFormu = document.getElementById('adres-bilgi-formu');
    var adresIptalBtn = document.getElementById('adres-iptal-btn');
    var adresBilgileri = null;

    if (devamEtBtn) {
        devamEtBtn.addEventListener('click', function() {
            devamEtBtn.style.display = 'none';
            if (adresFormKutusu) adresFormKutusu.style.display = 'block';
        });
    }

    if (adresIptalBtn) {
        adresIptalBtn.addEventListener('click', function() {
            if (adresFormKutusu) adresFormKutusu.style.display = 'none';
            if (devamEtBtn) devamEtBtn.style.display = '';
            if (odemeBtn) {
                odemeBtn.style.display = 'none';
                odemeBtn.disabled = true;
            }
        });
    }

    if (adresFormu) {
        adresFormu.addEventListener('submit', function(e) {
            e.preventDefault();
            // Adres bilgilerini topla
            adresBilgileri = {
                adres: document.getElementById('adres-adres').value.trim(),
                il: document.getElementById('adres-il').value.trim(),
                ilce: document.getElementById('adres-ilce').value.trim(),
                posta_kodu: document.getElementById('adres-posta').value.trim(),
                aciklama: document.getElementById('adres-aciklama').value.trim()
            };
            
            if (adresFormKutusu) adresFormKutusu.style.display = 'none';
            if (odemeBtn) {
                odemeBtn.style.display = '';
                odemeBtn.disabled = false;
            }
        });
    }

    if (odemeBtn) {
        odemeBtn.addEventListener('click', function() {
            odemeBtn.disabled = true;
            odemeBtn.textContent = 'Yönlendiriliyor...';
            
            // Müşteri bilgilerini ve adres bilgilerini topla
            var musteriAd = document.getElementById('musteri-ad').value.trim();
            var musteriSoyad = document.getElementById('musteri-soyad').value.trim();
            var musteriEposta = document.getElementById('musteri-eposta').value.trim();
            var musteriTelefon = document.getElementById('musteri-telefon').value.trim();
            
            // Form oluştur ve gönder
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("sepet.odeme.yap") }}';
            
            // CSRF token
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfInput);
            
            // Müşteri bilgileri
            ['ad_soyad', 'email', 'telefon'].forEach(function(field, index) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = field;
                input.value = [musteriAd + ' ' + musteriSoyad, musteriEposta, musteriTelefon][index];
                form.appendChild(input);
            });
            
            // Adres bilgileri
            if (adresBilgileri) {
                Object.keys(adresBilgileri).forEach(function(key) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = adresBilgileri[key];
                    form.appendChild(input);
                });
            }
            
            document.body.appendChild(form);
            form.submit();
        });
    }

    // - butonunda çöp kutusu hover
    document.querySelectorAll('.adet-azalt').forEach(function(btn) {
        btn.addEventListener('mouseenter', function() {
            var row = this.closest('tr');
            var span = row.querySelector('.adet-value');
            var adet = parseInt(span.textContent);
            if (adet === 1) {
                this.classList.add('delete-hover');
                this.querySelector('.azalt-ikon').innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 6V4C7 2.89543 7.89543 2 9 2H15C16.1046 2 17 2.89543 17 4V6M4 6H20M19 6V20C19 21.1046 18.1046 22 17 22H7C5.89543 22 5 21.1046 5 20V6H19Z" stroke="#fff" stroke-width="2"/><path d="M10 11V17" stroke="#fff" stroke-width="2" stroke-linecap="round"/><path d="M14 11V17" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>';
            }
        });
        
        btn.addEventListener('mouseleave', function() {
            var row = this.closest('tr');
            var span = row.querySelector('.adet-value');
            var adet = parseInt(span.textContent);
            if (adet === 1) {
                this.classList.remove('delete-hover');
                this.querySelector('.azalt-ikon').textContent = '-';
            }
        });
    });
});
</script>
@endsection
