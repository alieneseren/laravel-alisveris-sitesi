# PayThor B2C Entegrasyonu - Sorun Çözümleri

## 🚀 Tespit Edilen Sorunlar ve Çözümler

### 1. CSP (Content Security Policy) Güncellemesi

**Sorun:** PayThor API endpoint'leri CSP'de eksik veya kısıtlı

**Çözüm:** `next.config.js` dosyasını güncelleyin:

```javascript
// next.config.js - CSP Güncelleme
"connect-src 'self' https://api.stripe.com https://merchant-ui-api.stripe.com https://m.stripe.network https://api.paythor.com https://dev-api.paythor.com https://sandbox.paythor.com https://production.paythor.com https://api.ipify.org http://gc.kis.v2.scr.kaspersky-labs.com ws://gc.kis.v2.scr.kaspersky-labs.com",
```

### 2. PayThor Auth Service İyileştirmeleri

**Sorun:** Token expire kontrolü ve otomatik yenileme eksik

**Çözüm:** `src/lib/paythor-auth-direct.ts` dosyasına eklemeler:

```typescript
// Token geçerlilik kontrolü iyileştirmesi
public isTokenValid(): boolean {
  if (!this.token || !this.userData) return false;
  
  const now = new Date();
  const expireDate = new Date(this.userData.expire_date);
  
  // Token 5 dakika önce expire uyarısı
  const warningTime = new Date(expireDate.getTime() - 5 * 60 * 1000);
  
  if (now >= expireDate) {
    console.warn('PayThor token expired');
    this.logout();
    return false;
  }
  
  if (now >= warningTime) {
    console.warn('PayThor token will expire soon, consider refreshing');
  }
  
  return true;
}

// Otomatik token yenileme
public async refreshTokenIfNeeded(): Promise<boolean> {
  if (!this.isTokenValid()) {
    const userData = this.getUserData();
    if (userData?.email) {
      try {
        // Sessiz yenileme denemesi
        const result = await this.login(userData.email, ''); // Stored password gerekli
        return result.status === 'success';
      } catch (error) {
        console.error('Auto refresh failed:', error);
        return false;
      }
    }
  }
  return true;
}
```

### 3. Checkout Sayfası Hata Yönetimi

**Sorun:** Payment başarısızlık durumlarında kullanıcı deneyimi kötü

**Çözüm:** `src/app/checkout-paythor/page.tsx` dosyasına eklemeler:

```typescript
// Geliştirilmiş hata mesajları
const getErrorMessage = (error: any): string => {
  if (error?.response?.data?.message) {
    return error.response.data.message;
  }
  
  if (error?.message) {
    if (error.message.includes('Network Error')) {
      return 'İnternet bağlantınızı kontrol edin ve tekrar deneyin.';
    }
    if (error.message.includes('timeout')) {
      return 'İşlem zaman aşımına uğradı. Lütfen tekrar deneyin.';
    }
    if (error.message.includes('400')) {
      return 'Kart bilgilerinizi kontrol edin.';
    }
    if (error.message.includes('401')) {
      return 'Yetkilendirme hatası. Lütfen yeniden giriş yapın.';
    }
    return error.message;
  }
  
  return 'Beklenmeyen bir hata oluştu. Lütfen tekrar deneyin.';
};

// Payment retry mekanizması
const handlePaymentWithRetry = async (paymentData: any, maxRetries = 3) => {
  for (let attempt = 1; attempt <= maxRetries; attempt++) {
    try {
      setError('');
      setLoading(true);
      
      // Token geçerliliğini kontrol et
      if (!auth.isTokenValid()) {
        const refreshed = await auth.refreshTokenIfNeeded();
        if (!refreshed) {
          throw new Error('Oturum süresi doldu. Lütfen yeniden giriş yapın.');
        }
      }
      
      const result = await auth.processPayment(paymentData);
      
      if (result.status === 'success') {
        return result;
      } else {
        throw new Error(result.message || 'Payment failed');
      }
      
    } catch (error) {
      console.error(`Payment attempt ${attempt} failed:`, error);
      
      if (attempt === maxRetries) {
        throw error;
      }
      
      // Exponential backoff
      await new Promise(resolve => setTimeout(resolve, 1000 * attempt));
    } finally {
      setLoading(false);
    }
  }
};
```

### 4. Callback API İyileştirmesi

**Sorun:** Payment callback'inde database güncelleme logic'i eksik

**Çözüm:** `src/app/api/payment/callback/route.ts` dosyasını güncelleyin:

```typescript
import { NextRequest, NextResponse } from 'next/server'
import { PrismaClient } from '@prisma/client'

const prisma = new PrismaClient()

export async function POST(request: NextRequest) {
  try {
    const body = await request.json()
    console.log('PayThor Callback received:', body)
    
    const merchantReference = body.merchant_reference
    const paymentStatus = body.status || body.payment_status || 'unknown'
    const transactionId = body.transaction_id
    const amount = body.amount
    const currency = body.currency || 'TRY'
    
    if (!merchantReference) {
      console.error('Merchant reference not found in callback')
      return new NextResponse('Merchant reference not found', { status: 400 })
    }
    
    // Database'de siparişi bul ve güncelle
    try {
      const order = await prisma.order.findUnique({
        where: { merchantReference }
      })
      
      if (!order) {
        console.error('Order not found:', merchantReference)
        return new NextResponse('Order not found', { status: 404 })
      }
      
      // Sipariş durumunu güncelle
      const updatedOrder = await prisma.order.update({
        where: { id: order.id },
        data: {
          paymentStatus: paymentStatus,
          paymentMethod: 'paythor',
          transactionId: transactionId,
          paidAmount: amount ? parseFloat(amount) : order.totalAmount,
          updatedAt: new Date()
        }
      })
      
      console.log('Order updated successfully:', updatedOrder)
      
      // Başarılı ödeme ise email gönder (opsiyonel)
      if (paymentStatus === 'success' || paymentStatus === 'completed') {
        // Email service çağrısı
        // await sendOrderConfirmationEmail(order.userEmail, order.id)
      }
      
    } catch (dbError) {
      console.error('Database update error:', dbError)
      // Callback'i tekrar denemek için PayThor'a error dönmeyelim
      // return new NextResponse('Database error', { status: 500 })
    }
    
    // PayThor'a her zaman başarılı response dön
    return new NextResponse('OK', { status: 200 })
    
  } catch (error) {
    console.error('PayThor Callback Error:', error)
    return new NextResponse('Error', { status: 500 })
  }
}

export async function GET(request: NextRequest) {
  try {
    const searchParams = request.nextUrl.searchParams
    const merchantReference = searchParams.get('merchant_reference')
    const paymentStatus = searchParams.get('status') || searchParams.get('payment_status') || 'unknown'
    
    console.log('PayThor GET Callback received:', {
      merchant_reference: merchantReference,
      payment_status: paymentStatus
    })
    
    if (!merchantReference) {
      return new NextResponse('Merchant reference not found', { status: 400 })
    }
    
    // GET callback için de aynı logic
    // ... (POST ile aynı database güncelleme)
    
    return new NextResponse('OK', { status: 200 })
  } catch (error) {
    console.error('PayThor GET Callback Error:', error)
    return new NextResponse('Error', { status: 500 })
  }
}
```

### 5. Environment Variables Kontrolü

**Sorun:** PayThor API bilgileri eksik veya yanlış

**Çözüm:** `.env.local` dosyasına ekleyin:

```env
# PayThor Configuration
PAYTHOR_API_URL=https://dev-api.paythor.com
PAYTHOR_MERCHANT_ID=your_merchant_id
PAYTHOR_API_KEY=your_api_key
PAYTHOR_CALLBACK_URL=http://localhost:3000/api/payment/callback
PAYTHOR_SUCCESS_URL=http://localhost:3000/success
PAYTHOR_CANCEL_URL=http://localhost:3000/cancel
```

### 6. Prisma Schema Güncellemesi

**Sorun:** Order model'inde PayThor alanları eksik

**Çözüm:** `prisma/schema.prisma` dosyasına ekleyin:

```prisma
model Order {
  id              String   @id @default(cuid())
  merchantReference String @unique  // PayThor için
  paymentStatus   String   @default("pending")
  paymentMethod   String?
  transactionId   String?  // PayThor transaction ID
  paidAmount      Float?
  totalAmount     Float
  currency        String   @default("TRY")
  userEmail       String
  
  // ... diğer alanlar
  
  createdAt       DateTime @default(now())
  updatedAt       DateTime @updatedAt
  
  @@map("orders")
}
```

### 7. Test Senaryoları

**Önemli Test Noktaları:**

1. **Token Expire Testi:**
   - Token süresi dolduğunda otomatik yenileme
   - Login sayfasına yönlendirme

2. **Network Error Testi:**
   - İnternet bağlantısı kesik
   - API endpoint erişilemez

3. **Payment Error Testi:**
   - Yetersiz bakiye
   - Kart hatası
   - 3D Secure başarısız

4. **Callback Testi:**
   - Başarılı ödeme callback'i
   - Başarısız ödeme callback'i
   - Eksik merchant_reference

### 8. Production Hazırlık

**Production'a geçerken değiştirilmesi gerekenler:**

1. `PAYTHOR_API_URL=https://api.paythor.com` (prod URL)
2. Gerçek merchant bilgileri
3. HTTPS callback URL'leri
4. CSP'de production domain'leri

### 9. Debug Console Commands

**Geliştirme sırasında console'da test için:**

```javascript
// PayThor auth durumu kontrolü
PayThorAuth.getInstance().isAuthenticated()

// Token bilgileri
PayThorAuth.getInstance().getUserData()

// Manuel logout
PayThorAuth.getInstance().logout()

// Token geçerlilik kontrolü
PayThorAuth.getInstance().isTokenValid()
```

## 📋 Hızlı Çözüm Checklist

- [ ] CSP ayarlarını güncelle
- [ ] Auth service'e token expire kontrolü ekle
- [ ] Checkout sayfasına retry mekanizması ekle
- [ ] Callback API'sini database ile entegre et
- [ ] Environment variables'ları kontrol et
- [ ] Prisma schema'ya PayThor alanları ekle
- [ ] Error handling'i iyileştir
- [ ] Test senaryolarını çalıştır

Bu düzeltmeler PayThor entegrasyonunuzdaki ana sorunları çözecektir. Her adımı sırasıyla uygulayarak stabil bir ödeme sistemi elde edebilirsiniz.
