#!/bin/bash

# Portfolio Deployment Script
# Auto-deployed via GitHub webhook

echo "🚀 Webhook deployment başlatılıyor..."
echo "📅 Deployment zamanı: $(date)"

# Set working directory
cd /home/tunakdu/web/akduhan.com/public_html

# Lock mechanism
LOCK_FILE="/tmp/webhook-deploy.lock"
if [ -f "$LOCK_FILE" ]; then
    echo "⚠️ Başka deployment çalışıyor, çıkılıyor..."
    exit 1
fi
echo $$ > "$LOCK_FILE"

# Deployment function
deploy() {
    echo "⏸️ Supervisor durdur"
    sudo supervisorctl stop laravel-worker:* laravel-scheduler || true
    
    echo "📥 Git pull"
    git pull origin master
    
    echo "🔍 Git durumu kontrol et"
    git status --porcelain
    echo "📋 Son commit bilgisi"
    git log -1 --oneline
    
    echo "📦 Composer install"
    COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction
    
    echo "🎨 NPM install ve build"
    npm install --production
    
    echo "🏗️ Vite build başlatılıyor"
    npm run build
    
    echo "📁 Build dosyaları kontrol et"
    if [ -d "public/build" ]; then
        echo "✅ Build dosyaları mevcut"
        ls -la public/build/ | head -5
    else
        echo "⚠️ Build dosyaları bulunamadı"
    fi
    
    echo "🧹 Cache temizle"
    php artisan config:clear || true
    php artisan cache:clear || true
    php artisan view:clear || true
    php artisan route:clear || true
    php artisan queue:clear --force || true
    
    echo "🗄️ Migration"
    php artisan migrate --force
    
    echo "🔧 IMAP config kontrol"
    if ! grep -q "imap.*=>" config/mail.php; then
        echo "IMAP section ekleniyor..."
        cp config/mail.php config/mail.php.backup
        php -r "
        \$content = file_get_contents('config/mail.php');
        \$imap = \"
    'imap' => [
        'host' => env('IMAP_HOST'),
        'port' => env('IMAP_PORT', 993),
        'username' => env('IMAP_USERNAME'),
        'password' => env('IMAP_PASSWORD'),
        'encryption' => env('IMAP_ENCRYPTION', 'ssl'),
    ],

\";
        \$content = str_replace('];', \$imap . '];', \$content);
        file_put_contents('config/mail.php', \$content);
        echo 'IMAP section eklendi\n';
        "
    else
        echo "IMAP section mevcut"
    fi
    
    echo "📁 Dosya izinleri"
    chown -R tunakdu:www-data ./
    chmod -R 755 ./
    chmod -R 775 storage/ bootstrap/cache/ public/build/
    chmod +x artisan
    
    echo "⚡ Cache oluştur"
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    echo "🚀 Supervisor başlat"
    sudo supervisorctl start laravel-worker:* laravel-scheduler || true
    sleep 3
    
    echo "📊 Supervisor durumu kontrol et"
    sudo supervisorctl status | grep laravel
    
    # Servisler çalışıyor mu kontrol et
    if sudo supervisorctl status | grep -q "laravel.*RUNNING"; then
        echo "✅ Laravel servisleri çalışıyor"
    else
        echo "⚠️ Laravel servisleri başlatılamadı, yeniden dene"
        sudo supervisorctl restart laravel-worker:* laravel-scheduler || true
        sleep 2
        sudo supervisorctl status | grep laravel
    fi
    
    echo "🔄 Queue restart"
    php artisan queue:restart
    
    echo "📧 IMAP test"
    php artisan email:sync --force
    
    echo "✅ Deployment başarılı! $(date)"
}

# Run deployment
if deploy 2>&1 | tee /tmp/deploy.log; then
    echo "SUCCESS: Deployment completed" > /tmp/deploy.status
else
    echo "ERROR: Deployment failed" > /tmp/deploy.status
    exit 1
fi

# Cleanup
rm -f "$LOCK_FILE"

echo "🎉 Webhook deployment tamamlandı!"