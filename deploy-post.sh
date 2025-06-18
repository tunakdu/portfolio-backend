#!/bin/bash

# Post-deployment script
# Bu script sunucuda çalıştırılmalıdır

echo "🚀 Post-deployment script başlatılıyor..."

# Composer dependencies yükle (vendor klasörü FTP ile gönderilmedi)
echo "📦 Composer dependencies yükleniyor..."
composer install --no-dev --optimize-autoloader --no-interaction

# .env dosyasını kontrol et
if [ ! -f .env ]; then
    if [ -f .env.production ]; then
        echo "📁 .env.production dosyası .env olarak kopyalanıyor..."
        cp .env.production .env
    else
        echo "❌ .env dosyası bulunamadı! Lütfen .env dosyasını manuel olarak oluşturun."
        exit 1
    fi
fi

# Laravel key generate
echo "🔑 Laravel application key oluşturuluyor..."
php artisan key:generate --force

# Cache temizle
echo "🧹 Cache temizleniyor..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize
echo "⚡ Uygulama optimize ediliyor..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrate
echo "📊 Veritabanı migrate ediliyor..."
php artisan migrate --force

# Storage link
echo "🔗 Storage link oluşturuluyor..."
php artisan storage:link

# Permissions
echo "🔐 Dosya izinleri ayarlanıyor..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public

# Clear compiled files
echo "🗑️ Derlenmiş dosyalar temizleniyor..."
php artisan clear-compiled

echo "✅ Post-deployment script tamamlandı!"
echo ""
echo "🎉 Deployment başarıyla tamamlandı!"
echo "🌐 Web siteniz artık güncel!"