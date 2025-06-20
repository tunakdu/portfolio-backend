# 🔄 Webhook Deployment Setup

Bu dokümanda GitHub webhook ile otomatik deployment sisteminin nasıl kurulacağı anlatılıyor.

## 🚀 1. Canlı Sunucuda Kurulum

### Dosyaları Yerleştir
```bash
cd /home/tunakdu/web/akduhan.com/public_html

# Deploy script'i executable yap
chmod +x deploy.sh

# Webhook PHP dosyasını test et
php webhook.php # (test için)
```

### Sudo İzinleri Ekle
```bash
# Sudo dosyasını düzenle
sudo visudo

# Bu satırları ekle:
tunakdu ALL=(ALL) NOPASSWD: /usr/bin/supervisorctl
```

## 🔗 2. GitHub Webhook Konfigürasyonu

### Repository Settings'e Git:
1. GitHub → Repository → Settings
2. Webhooks → Add webhook

### Webhook Ayarları:
- **Payload URL**: `https://akduhan.com/webhook.php`
- **Content type**: `application/json`
- **Secret**: `portfolio_deploy_2024_secure_key_tunakdu`
- **Events**: Just the push event
- **Active**: ✅

## 🔧 3. Test ve Doğrulama

### Manuel Test:
```bash
# Deploy script'i test et
cd /home/tunakdu/web/akduhan.com/public_html
./deploy.sh
```

### Webhook Test:
```bash
# Log dosyalarını izle
tail -f webhook.log
tail -f /tmp/webhook-deploy-output.log
```

### GitHub Test:
1. Bir commit yap ve push et
2. `webhook.log` dosyasını kontrol et
3. Deployment'ın çalıştığını doğrula

## 📊 4. Monitoring

### Log Dosyaları:
- `webhook.log` - Webhook istekleri
- `/tmp/webhook-deploy-output.log` - Deploy çıktıları
- `/tmp/deploy.status` - Son deployment durumu

### Status Kontrol:
```bash
# Son deployment durumu
cat /tmp/deploy.status

# Son deploy log
tail -20 /tmp/webhook-deploy-output.log

# Supervisor durumu
sudo supervisorctl status
```

## 🛡️ 5. Security

### IP Whitelist:
Webhook handler GitHub IP'lerini kontrol eder.

### Secret Verification:
GitHub webhook secret'ı ile imza doğrulaması yapar.

### File Permissions:
```bash
chmod 644 webhook.php
chmod 755 deploy.sh
```

## 🔄 6. Workflow

1. **Code Change** → Push to master
2. **GitHub** → Webhook trigger
3. **webhook.php** → Receives and validates request
4. **deploy.sh** → Executes deployment
5. **Result** → Logged and monitored

## ⚠️ Troubleshooting

### Common Issues:

1. **Permission Denied**:
   ```bash
   chmod +x deploy.sh
   sudo chown tunakdu:www-data deploy.sh
   ```

2. **Sudo Issues**:
   ```bash
   # Check sudo config
   sudo -l
   ```

3. **Webhook Not Triggering**:
   - Check GitHub webhook delivery logs
   - Verify webhook.php is accessible
   - Check server error logs

4. **Deployment Fails**:
   ```bash
   # Check deploy logs
   tail -50 /tmp/webhook-deploy-output.log
   
   # Manual test
   ./deploy.sh
   ```

## 🎯 Benefits

- ✅ **Otomatik deployment** her push'ta
- ✅ **Güvenli** GitHub signature verification
- ✅ **Hızlı** GitHub Actions'dan çok daha hızlı
- ✅ **Basit** karmaşık YAML yok
- ✅ **Reliable** local script execution
- ✅ **Monitored** comprehensive logging