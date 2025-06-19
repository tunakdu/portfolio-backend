#!/bin/bash

# VPS Laravel Kurulum Scripti
# Ubuntu 20.04/22.04 için

echo "🚀 VPS Laravel kurulumu başlatılıyor..."

# System update
echo "📦 Sistem güncelleniyor..."
sudo apt update && sudo apt upgrade -y

# Nginx kurulumu
echo "🌐 Nginx kuruluyor..."
sudo apt install nginx -y

# PHP 8.2 kurulumu
echo "🐘 PHP 8.2 kuruluyor..."
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-intl php8.2-bcmath php8.2-gd php8.2-imap -y

# Composer kurulumu
echo "🎵 Composer kuruluyor..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js kurulumu
echo "⚡ Node.js kuruluyor..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# MySQL kurulumu
echo "🗄️ MySQL kuruluyor..."
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Git kurulumu
sudo apt install git -y

# Laravel projesi klonla
echo "📂 Laravel projesi klonlanıyor..."
cd /var/www
sudo git clone https://github.com/tunakdu/portfolio-backend.git portfolio
sudo chown -R www-data:www-data /var/www/portfolio
sudo chmod -R 755 /var/www/portfolio
sudo chmod -R 775 /var/www/portfolio/storage
sudo chmod -R 775 /var/www/portfolio/bootstrap/cache

# .env dosyası oluştur
echo "📄 .env dosyası oluşturuluyor..."
cd /var/www/portfolio
sudo cp .env.example .env

# Composer install
echo "📦 Dependencies yükleniyor..."
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm install
sudo -u www-data npm run build

# Laravel key generate
sudo -u www-data php artisan key:generate

# Nginx config
echo "⚙️ Nginx konfigürasyonu..."
sudo tee /etc/nginx/sites-available/akduhan.com > /dev/null <<EOF
server {
    listen 80;
    server_name akduhan.com www.akduhan.com;
    root /var/www/portfolio/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Nginx site'ı aktifleştir
sudo ln -s /etc/nginx/sites-available/akduhan.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl enable nginx

# PHP-FPM başlat
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm

echo "✅ VPS kurulumu tamamlandı!"
echo "🔧 Şimdi yapmanız gerekenler:"
echo "1. .env dosyasını düzenleyin"
echo "2. MySQL database oluşturun"
echo "3. php artisan migrate çalıştırın"
echo "4. SSL sertifikası kurun (Let's Encrypt)"