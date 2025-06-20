<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Article;
use App\Models\SiteSetting;
use App\Models\Message;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Kullanıcısı
        $this->call([
            AdminUserSeeder::class,
        ]);

        // 2. Kategoriler
        $categories = [
            ['name' => 'Web Uygulaması', 'slug' => 'web-uygulamasi', 'description' => 'Frontend ve backend teknolojileri ile geliştirilmiş web uygulamaları', 'color' => 'bg-blue-100 text-blue-800', 'icon' => 'globe', 'order' => 1, 'is_active' => true],
            ['name' => 'Mobil Uygulama', 'slug' => 'mobil-uygulama', 'description' => 'iOS ve Android platformları için geliştirilen mobil uygulamalar', 'color' => 'bg-green-100 text-green-800', 'icon' => 'smartphone', 'order' => 2, 'is_active' => true],
            ['name' => 'API & Backend', 'slug' => 'api-backend', 'description' => 'RESTful API ve backend sistemleri', 'color' => 'bg-purple-100 text-purple-800', 'icon' => 'server', 'order' => 3, 'is_active' => true],
            ['name' => 'E-ticaret', 'slug' => 'e-ticaret', 'description' => 'Online mağaza ve e-ticaret platformları', 'color' => 'bg-orange-100 text-orange-800', 'icon' => 'shopping-cart', 'order' => 4, 'is_active' => true],
            ['name' => 'Desktop Uygulama', 'slug' => 'desktop-uygulama', 'description' => 'Windows, macOS ve Linux için masaüstü uygulamaları', 'color' => 'bg-indigo-100 text-indigo-800', 'icon' => 'monitor', 'order' => 5, 'is_active' => true]
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // 3. Projeler
        $projects = [
            [
                'title' => 'E-ticaret Platformu',
                'description' => 'Laravel ve Vue.js ile geliştirilmiş modern e-ticaret platformu',
                'category' => 'E-ticaret',
                'long_description' => 'Kullanıcı yönetimi, ürün kataloğu, sepet sistemi, ödeme entegrasyonları, stok yönetimi, sipariş takibi ve kapsamlı admin paneli içeren tam özellikli e-ticaret platformu. Modern UI/UX tasarımı ve responsive yapı.',
                'technologies' => json_encode(['Laravel', 'Vue.js', 'MySQL', 'Stripe', 'PayPal', 'Redis', 'Tailwind CSS']),
                'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop',
                'live_url' => 'https://demo-ecommerce.com',
                'github_url' => 'https://github.com/tunahanakduhan/ecommerce-platform',
                'status' => 'completed',
                'featured' => true
            ],
            [
                'title' => 'Task Management API',
                'description' => 'Proje yönetimi için kapsamlı RESTful API sistemi',
                'category' => 'API & Backend',
                'long_description' => 'JWT authentication, role-based permissions, real-time notifications, dosya yükleme, proje yönetimi, görev atama ve takip özellikleri içeren profesyonel API. OpenAPI documentation ve comprehensive testing coverage.',
                'technologies' => json_encode(['Laravel', 'MySQL', 'Redis', 'WebSockets', 'JWT', 'Swagger', 'PHPUnit']),
                'image' => 'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=800&h=600&fit=crop',
                'live_url' => null,
                'github_url' => 'https://github.com/tunahanakduhan/task-management-api',
                'status' => 'completed',
                'featured' => true
            ],
            [
                'title' => 'Portfolio Website',
                'description' => 'Modern ve responsive kişisel portfolio websitesi',
                'category' => 'Web Uygulaması',
                'long_description' => 'Vue.js 3 Composition API, Tailwind CSS, admin paneli, blog sistemi, proje showcase, iletişim formu ve dinamik içerik yönetimi. SEO optimized ve performans odaklı.',
                'technologies' => json_encode(['Laravel', 'Vue.js', 'Tailwind CSS', 'MySQL', 'Vite', 'Sanctum']),
                'image' => 'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?w=800&h=600&fit=crop',
                'live_url' => 'https://tunahanakduhan.com',
                'github_url' => 'https://github.com/tunahanakduhan/portfolio',
                'status' => 'in_progress',
                'featured' => true
            ],
            [
                'title' => 'Mobil Fitness Uygulaması',
                'description' => 'React Native ile geliştirilmiş fitness tracking uygulaması',
                'category' => 'Mobil Uygulama',
                'long_description' => 'Workout planları, egzersiz takibi, kalori hesaplayıcı, progress tracking, sosyal özellikler ve kişiselleştirilmiş antrenman programları. Firebase backend integration.',
                'technologies' => json_encode(['React Native', 'Node.js', 'MongoDB', 'Firebase', 'Redux', 'Expo']),
                'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&h=600&fit=crop',
                'live_url' => null,
                'github_url' => 'https://github.com/tunahanakduhan/fitness-mobile-app',
                'status' => 'in_progress',
                'featured' => false
            ],
            [
                'title' => 'Real Estate CRM',
                'description' => 'Emlak sektörü için özel CRM sistemi',
                'category' => 'Web Uygulaması',
                'long_description' => 'Müşteri yönetimi, emlak portföyü, randevu sistemi, komisyon hesaplamaları, raporlama ve analitik dashboard. Multi-agent support ve lead management.',
                'technologies' => json_encode(['Laravel', 'Vue.js', 'PostgreSQL', 'Chart.js', 'Bootstrap']),
                'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&h=600&fit=crop',
                'live_url' => 'https://crm-demo.com',
                'github_url' => 'https://github.com/tunahanakduhan/real-estate-crm',
                'status' => 'completed',
                'featured' => false
            ],
            [
                'title' => 'Desktop Note App',
                'description' => 'Electron ile geliştirilmiş not alma uygulaması',
                'category' => 'Desktop Uygulama',
                'long_description' => 'Cross-platform desktop uygulaması. Rich text editor, kategorileme, arama, senkronizasyon, dark mode ve shortcut desteği. Local storage ve cloud backup.',
                'technologies' => json_encode(['Electron', 'Vue.js', 'SQLite', 'Node.js', 'Quill.js']),
                'image' => 'https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?w=800&h=600&fit=crop',
                'live_url' => null,
                'github_url' => 'https://github.com/tunahanakduhan/desktop-notes',
                'status' => 'completed',
                'featured' => false
            ]
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        // 4. Yetenekler
        $skills = [
            [
                'title' => 'Backend Development',
                'description' => 'Laravel, PHP, Node.js ile server-side application development',
                'icon' => 'server',
                'color' => '#8B5CF6',
                'technologies' => json_encode(['Laravel', 'PHP', 'Node.js', 'Express.js', 'API Design']),
                'proficiency_level' => 5,
                'order' => 1,
                'is_active' => true
            ],
            [
                'title' => 'Frontend Development',
                'description' => 'Vue.js, React, JavaScript ile modern user interface development',
                'icon' => 'monitor',
                'color' => '#06B6D4',
                'technologies' => json_encode(['Vue.js', 'React', 'JavaScript', 'Tailwind CSS', 'HTML5', 'CSS3']),
                'proficiency_level' => 5,
                'order' => 2,
                'is_active' => true
            ],
            [
                'title' => 'Database Management',
                'description' => 'MySQL, PostgreSQL, MongoDB ile database design ve optimization',
                'icon' => 'database',
                'color' => '#EF4444',
                'technologies' => json_encode(['MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'Database Design']),
                'proficiency_level' => 4,
                'order' => 3,
                'is_active' => true
            ],
            [
                'title' => 'Mobile Development',
                'description' => 'React Native, Flutter ile cross-platform mobile applications',
                'icon' => 'smartphone',
                'color' => '#10B981',
                'technologies' => json_encode(['React Native', 'Flutter', 'Expo', 'Mobile UI/UX']),
                'proficiency_level' => 4,
                'order' => 4,
                'is_active' => true
            ],
            [
                'title' => 'DevOps & Deployment',
                'description' => 'Docker, AWS, Linux ile deployment ve infrastructure management',
                'icon' => 'cloud',
                'color' => '#F59E0B',
                'technologies' => json_encode(['Docker', 'AWS', 'Linux', 'Git', 'CI/CD', 'Nginx']),
                'proficiency_level' => 3,
                'order' => 5,
                'is_active' => true
            ],
            [
                'title' => 'API Development',
                'description' => 'RESTful API, GraphQL, microservices architecture design',
                'icon' => 'link',
                'color' => '#6366F1',
                'technologies' => json_encode(['REST API', 'GraphQL', 'Microservices', 'JWT', 'OAuth']),
                'proficiency_level' => 5,
                'order' => 6,
                'is_active' => true
            ]
        ];

        foreach ($skills as $skillData) {
            Skill::create($skillData);
        }

        // 5. Makaleler
        $articles = [
            [
                'title' => 'Laravel 11 ile Modern API Geliştirme',
                'slug' => 'laravel-11-modern-api-gelistirme',
                'content' => '<h2>Laravel 11 Yenilikleri</h2><p>Laravel 11 ile gelen yeni özellikler ve API geliştirmede kullanım senaryoları.</p><h3>Yeni Özellikler</h3><ul><li>Improved performance</li><li>Better error handling</li><li>Enhanced security features</li></ul><h3>API Development Best Practices</h3><p>Modern API geliştirme sürecinde dikkat edilmesi gereken noktalar ve Laravel 11 ile bunları nasıl uygulayabileceğimiz.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&h=400&fit=crop',
                'is_published' => true,
                'published_at' => now()
            ],
            [
                'title' => 'Vue.js 3 Composition API Deep Dive',
                'slug' => 'vuejs-3-composition-api-deep-dive',
                'content' => '<h2>Composition API Nedir?</h2><p>Vue.js 3 ile gelen Composition API, component logic organizasyonu için yeni bir yaklaşım.</p><h3>Setup Function</h3><p>Composition API\'nin kalbi olan setup fonksiyonu ve kullanım alanları.</p><h3>Reactivity System</h3><p>Vue 3\'te reactivity sisteminin nasıl çalıştığı ve performans iyileştirmeleri.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1627398242454-45a1465c2479?w=800&h=400&fit=crop',
                'is_published' => true,
                'published_at' => now()->subDays(3)
            ],
            [
                'title' => 'RESTful API Tasarım Prensipleri',
                'slug' => 'restful-api-tasarim-prensipleri',
                'content' => '<h2>REST Nedir?</h2><p>Representational State Transfer (REST) mimarisinin temel prensipleri.</p><h3>HTTP Methods</h3><p>GET, POST, PUT, PATCH, DELETE methodlarının doğru kullanımı.</p><h3>Status Codes</h3><p>API response\'larında doğru HTTP status code kullanımı.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&h=400&fit=crop',
                'is_published' => true,
                'published_at' => now()->subWeek()
            ],
            [
                'title' => 'JavaScript ES2024 Yeni Özellikleri',
                'slug' => 'javascript-es2024-yeni-ozellikleri',
                'content' => '<h2>ES2024 ile Neler Geldi?</h2><p>JavaScript\'in en yeni versiyonunda yer alan özellikler ve kullanım alanları.</p><h3>Array Grouping</h3><p>Array.prototype.group() metoduyla array elementlerini gruplandırma.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1579468118864-1b9ea3c0db4a?w=800&h=400&fit=crop',
                'is_published' => true,
                'published_at' => now()->subDays(5)
            ],
            [
                'title' => 'Docker ile Laravel Development',
                'slug' => 'docker-laravel-development',
                'content' => '<h2>Docker Neden Önemli?</h2><p>Containerization teknolojisinin development süreçlerine katkıları.</p><h3>Laravel Sail</h3><p>Laravel\'in resmi Docker development environment\'ı olan Sail kurulumu ve kullanımı.</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1605745341112-85968b19335a?w=800&h=400&fit=crop',
                'is_published' => true,
                'published_at' => now()->subDays(10)
            ],
            [
                'title' => 'Microservices Architecture Patterns (Taslak)',
                'slug' => 'microservices-architecture-patterns',
                'content' => '<h2>Microservices Nedir?</h2><p>Bu makale henüz tamamlanmamıştır. Microservices mimarisi hakkında detaylı bilgiler yakında...</p>',
                'cover_image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&h=400&fit=crop',
                'is_published' => false,
                'published_at' => null
            ]
        ];

        foreach ($articles as $articleData) {
            Article::create($articleData);
        }

        // 6. Site Ayarları
        $siteSettings = [
            ['key' => 'site_name', 'value' => 'Tunahan Akduhan', 'type' => 'text'],
            ['key' => 'site_description', 'value' => 'Backend & API Developer - Laravel, Node.js, Vue.js', 'type' => 'text'],
            ['key' => 'name', 'value' => 'Tunahan Akduhan', 'type' => 'text'],
            ['key' => 'title', 'value' => 'Backend & API Developer', 'type' => 'text'],
            ['key' => 'email', 'value' => 'info@tunahanakduhan.com', 'type' => 'text'],
            ['key' => 'phone', 'value' => '+90 555 123 45 67', 'type' => 'text'],
            ['key' => 'location', 'value' => 'İstanbul, Türkiye', 'type' => 'text'],
            ['key' => 'hero_greeting', 'value' => '👋 Merhaba, Ben', 'type' => 'text'],
            ['key' => 'hero_description', 'value' => 'Laravel, Node.js, RESTful API\'lar ve modern backend teknolojileri ile güvenli ve ölçeklenebilir sistemler geliştiriyorum.', 'type' => 'text'],
            ['key' => 'github_url', 'value' => 'https://github.com/tunahanakduhan', 'type' => 'text'],
            ['key' => 'linkedin_url', 'value' => 'https://linkedin.com/in/tunahanakduhan', 'type' => 'text'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/tunahanakduhan', 'type' => 'text'],
            ['key' => 'cv_url', 'value' => '/storage/cv/tunahan-akduhan-cv.pdf', 'type' => 'text'],
            ['key' => 'contact_button_text', 'value' => 'İletişime Geç', 'type' => 'text'],
            ['key' => 'cv_button_text', 'value' => 'CV\'mi İndir', 'type' => 'text']
        ];

        foreach ($siteSettings as $settingData) {
            SiteSetting::create($settingData);
        }

        // 7. Örnek Mesajlar
        $messages = [
            [
                'name' => 'Ahmet Yılmaz',
                'email' => 'ahmet@example.com',
                'subject' => 'Proje İşbirliği Teklifi',
                'message' => 'Merhaba Tunahan, e-ticaret projesi için işbirliği yapmak istiyorum. Laravel ve Vue.js deneyiminiz çok etkileyici. Konuşabilir miyiz?',
                'is_read' => false
            ],
            [
                'name' => 'Elif Kaya',
                'email' => 'elif@example.com',
                'subject' => 'API Geliştirme Danışmanlığı',
                'message' => 'Şirketimiz için REST API geliştirme konusunda danışmanlık hizmeti alabilir miyiz? Özellikle Laravel experience\'ınız bizim için önemli.',
                'is_read' => true,
                'read_at' => now()->subDays(2)
            ],
            [
                'name' => 'Mehmet Demir',
                'email' => 'mehmet@example.com',
                'subject' => 'Mobil Uygulama Backend',
                'message' => 'React Native uygulamam için backend API\'ye ihtiyacım var. Portfolio\'nuzdaki Task Management API\'sı tam istediğim gibi. Fiyat bilgisi alabilir miyim?',
                'is_read' => false
            ],
            [
                'name' => 'Ayşe Öztürk',
                'email' => 'ayse@example.com',
                'subject' => 'Teşekkürler',
                'message' => 'Laravel blog yazılarınız çok faydalı. Özellikle API geliştirme konusundaki makaleniz işime çok yaradı. Teşekkür ederim!',
                'is_read' => true,
                'read_at' => now()->subDay()
            ],
            [
                'name' => 'Can Arslan',
                'email' => 'can@example.com',
                'subject' => 'Freelance Proje',
                'message' => 'Startup\'ımız için MVP geliştirmek istiyoruz. Laravel backend + Vue.js frontend kombini tam aradığımız şey. Müsait misiniz?',
                'is_read' => false
            ]
        ];

        foreach ($messages as $messageData) {
            Message::create($messageData);
        }

        echo "✅ Tüm tablolar başarıyla dolduruldu!\n";
        echo "👤 Admin: t@a.com / LcvrtVsvs16$\n";
        echo "📊 Kategoriler: " . count($categories) . " adet\n";
        echo "🚀 Projeler: " . count($projects) . " adet\n";
        echo "💪 Yetenekler: " . count($skills) . " adet\n";
        echo "📝 Makaleler: " . count($articles) . " adet\n";
        echo "⚙️ Site Ayarları: " . count($siteSettings) . " adet\n";
        echo "📨 Mesajlar: " . count($messages) . " adet\n";
    }
}