# Stripe Ödeme Sistemi Kurulumu

Bu proje Stripe ödeme sistemi içerir. Kurulum için aşağıdaki adımları takip edin:

## 1. Stripe Hesabı Oluşturma

1. [Stripe Dashboard](https://dashboard.stripe.com/register) adresinden hesap oluşturun
2. Test modu aktif olduğundan emin olun

## 2. API Anahtarlarını Alma

1. Stripe Dashboard'da `Developers > API Keys` bölümüne gidin
2. Test modunda:
   - **Publishable key** (pk_test_ ile başlar)
   - **Secret key** (sk_test_ ile başlar)
   
## 3. Webhook Kurulumu (Opsiyonel)

1. `Developers > Webhooks` bölümüne gidin
2. `Add endpoint` butonuna tıklayın
3. Endpoint URL: `https://yourdomain.com/stripe/webhook`
4. Events to send: `payment_intent.succeeded`
5. Webhook signing secret'ı kopyalayın

## 4. .env Dosyası Ayarları

`.env` dosyasında aşağıdaki ayarları yapın:

```bash
# Stripe Payment Configuration
STRIPE_KEY=pk_test_your_stripe_publishable_key_here
STRIPE_SECRET=sk_test_your_stripe_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_stripe_webhook_secret_here
```

## 5. Test Kartları

Stripe test ortamında kullanabileceğiniz kartlar:

- **Başarılı Ödeme**: `4242 4242 4242 4242`
- **Tarih**: Gelecekteki herhangi bir tarih (örn: 12/25)
- **CVC**: Herhangi bir 3 haneli sayı (örn: 123)

## 6. Ödeme Akışı

1. `/stripe/checkout` - Adres bilgileri formu
2. `/stripe/payment` - Kart bilgileri ve ödeme
3. `/stripe/success` - Başarı sayfası

## Güvenlik Notları

- **ASLA** gerçek API anahtarlarınızı repository'de saklamayın
- Production'da SSL sertifikası kullanın
- Webhook endpoint'lerinizi güvenceye alın
- Regular olarak access log'larını kontrol edin

## Sorun Giderme

- Browser console'da hata varsa kontrol edin
- Laravel log dosyalarını inceleyin: `storage/logs/laravel.log`
- Stripe Dashboard'da payment activity'leri kontrol edin

## Daha Fazla Bilgi

- [Stripe Documentation](https://stripe.com/docs)
- [Laravel Cashier](https://laravel.com/docs/cashier) (İleri seviye entegrasyon için)
