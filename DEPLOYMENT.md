# 🚀 Deployment Kurulumu

Bu proje GitHub Actions ile otomatik FTP deployment kullanıyor. Her `main` branch'e push yapıldığında otomatik olarak production sunucusuna deploy edilir.

## ⚙️ GitHub Secrets Kurulumu

GitHub repository'nizde aşağıdaki secrets'ları ayarlamanız gerekiyor:

### Repository Settings > Secrets and Variables > Actions > New repository secret

1. **FTP_SERVER**
   - Değer: `ftp.yourdomain.com` (FTP sunucu adresi)

2. **FTP_USERNAME** 
   - Değer: `your-ftp-username` (FTP kullanıcı adı)

3. **FTP_PASSWORD**
   - Değer: `your-ftp-password` (FTP şifre)

4. **FTP_SERVER_DIR**
   - Değer: `/public_html/` veya `/htdocs/` (Sunucudaki hedef klasör)

## 📁 Sunucu Kurulumu

### 1. .env Dosyası
Sunucunuzda `.env` dosyası oluşturun ve aşağıdaki ayarları yapın:

```env
APP_NAME="Portfolio"
APP_ENV=production
APP_KEY=base64:... # php artisan key:generate ile oluşturulacak
APP_DEBUG=false
APP_TIMEZONE=Europe/Istanbul
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Portfolio"

# Gmail API
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
```

### 2. Post-Deployment Script
İlk deployment'tan sonra sunucuda şu komutları çalıştırın:

```bash
# Sunucunuzda SSH ile bağlanın
ssh username@yourdomain.com

# Sitenizin klasörüne gidin
cd public_html # veya htdocs

# Post-deployment script'i çalıştırın
chmod +x deploy-post.sh
./deploy-post.sh
```

### 3. Cron Job (Gmail Sync için)
Gmail otomatik senkronizasyonu için cron job ekleyin:

```bash
# Crontab'ı düzenleyin
crontab -e

# Her 2 dakikada bir Gmail sync
*/2 * * * * cd /path/to/your/project && php artisan gmail:sync > /dev/null 2>&1
```

## 🔄 Deployment Süreci

1. **Kod değişikliği yap**
2. **Commit ve push**:
   ```bash
   git add .
   git commit -m "Your changes"
   git push origin main
   ```
3. **GitHub Actions otomatik çalışacak**
4. **2-3 dakika sonra siteniz güncel olacak**

## 📊 Deployment Durumu

GitHub repository'nizde **Actions** sekmesinden deployment durumunu takip edebilirsiniz.

## ⚠️ Önemli Notlar

- İlk deployment'tan sonra mutlaka sunucuda `./deploy-post.sh` script'ini çalıştırın
- Database migration'ları otomatik çalışır
- Asset'ler (CSS/JS) otomatik build edilir
- Gmail API credentials'larını `.env` dosyasına eklemeyi unutmayın

## 🆘 Sorun Giderme

### Deployment Başarısız Olursa:
1. GitHub Actions logs'larını kontrol edin
2. FTP credentials'larının doğru olduğundan emin olun
3. Sunucuda dosya izinlerini kontrol edin

### Site Açılmıyorsa:
1. `.env` dosyasının mevcut olduğundan emin olun
2. `php artisan key:generate` komutunu çalıştırın
3. Dosya izinlerini kontrol edin: `chmod -R 755 storage bootstrap/cache`

## 📞 Destek

Herhangi bir sorunla karşılaştığınızda lütfen GitHub Issues kısmından bildirin.