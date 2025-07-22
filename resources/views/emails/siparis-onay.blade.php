<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Onayı</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .footer {
            background-color: #6c757d;
            color: white;
            text-align: center;
            padding: 15px;
            border-radius: 0 0 5px 5px;
        }
        .order-details {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .order-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .total {
            font-weight: bold;
            font-size: 18px;
            color: #007bff;
            text-align: right;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #007bff;
        }
        .address-info {
            background-color: #e9ecef;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📦 Siparişiniz Onaylandı!</h1>
        <p>Sipariş No: #{{ $siparis->id }}</p>
    </div>

    <div class="content">
        <p>Merhaba <strong>{{ json_decode($siparis->fatura_bilgileri, true)['ad_soyad'] ?? 'Değerli Müşterimiz' }}</strong>,</p>
        
        <p>Siparişiniz başarıyla alındı ve ödemeniz onaylandı. Siparişiniz en kısa sürede hazırlanarak kargoya verilecektir.</p>

        <div class="order-details">
            <h3>📋 Sipariş Detayları</h3>
            
            <div class="order-item">
                <strong>Sipariş Tarihi:</strong> {{ $siparis->created_at->format('d.m.Y H:i') }}
            </div>
            
            <div class="order-item">
                <strong>Ödeme Durumu:</strong> 
                <span style="color: #28a745;">✅ Onaylandı</span>
            </div>
            
            <div class="order-item">
                <strong>Sipariş Durumu:</strong> 
                <span style="color: #007bff;">📦 {{ ucfirst($siparis->durum) }}</span>
            </div>

            @if($siparis->siparisUrunleri && $siparis->siparisUrunleri->count() > 0)
            <h4 style="margin-top: 20px;">🛍️ Sipariş Edilen Ürünler:</h4>
            @foreach($siparis->siparisUrunleri as $item)
            <div class="order-item">
                <strong>{{ $item->urun->ad ?? 'Ürün' }}</strong><br>
                <small>Adet: {{ $item->adet }} | Birim Fiyat: {{ number_format($item->birim_fiyat, 2) }} TL</small><br>
                <span style="color: #007bff;">Ara Toplam: {{ number_format($item->adet * $item->birim_fiyat, 2) }} TL</span>
            </div>
            @endforeach
            @endif

            <div class="total">
                Toplam Tutar: {{ number_format($siparis->toplam_tutar, 2) }} TL
            </div>
        </div>

        @if($siparis->teslimat_adresi)
        <div class="address-info">
            <h4>🚚 Teslimat Bilgileri</h4>
            <p><strong>Adres:</strong> {{ $siparis->teslimat_adresi }}</p>
            @if($siparis->teslimat_telefonu)
            <p><strong>Telefon:</strong> {{ $siparis->teslimat_telefonu }}</p>
            @endif
        </div>
        @endif

        <p>Kargo takip numaranız hazır olduğunda size bildirilecektir. Siparişinizle ilgili sorularınız için bizimle iletişime geçebilirsiniz.</p>
        
        <p style="margin-top: 20px;">Alışverişiniz için teşekkür ederiz! 🙏</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Laravel Pazaryeri</p>
        <p>Bu e-posta otomatik olarak gönderilmiştir. Lütfen yanıtlamayınız.</p>
    </div>
</body>
</html>
