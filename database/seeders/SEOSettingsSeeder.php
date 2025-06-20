<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SEOSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seoSettings = [
            // Meta Tags
            ['key' => 'meta_description', 'value' => 'Tunahan Akduhan - Laravel, Node.js, Vue.js ile backend development ve API geliştirme uzmanı. Modern web uygulamaları ve RESTful API hizmetleri.', 'type' => 'textarea'],
            ['key' => 'meta_keywords', 'value' => 'laravel, nodejs, vuejs, backend developer, api development, php, javascript, web development, tunahan akduhan', 'type' => 'text'],
            ['key' => 'meta_author', 'value' => 'Tunahan Akduhan', 'type' => 'text'],
            ['key' => 'meta_robots', 'value' => 'index, follow', 'type' => 'text'],
            
            // Open Graph Tags
            ['key' => 'og_title', 'value' => 'Tunahan Akduhan - Backend & API Developer', 'type' => 'text'],
            ['key' => 'og_description', 'value' => 'Laravel, Node.js, Vue.js ile backend development ve API geliştirme uzmanı. Modern web uygulamaları ve RESTful API hizmetleri.', 'type' => 'textarea'],
            ['key' => 'og_image', 'value' => '/images/og-image.jpg', 'type' => 'text'],
            ['key' => 'og_type', 'value' => 'website', 'type' => 'text'],
            ['key' => 'og_locale', 'value' => 'tr_TR', 'type' => 'text'],
            
            // Twitter Card Tags
            ['key' => 'twitter_card', 'value' => 'summary_large_image', 'type' => 'text'],
            ['key' => 'twitter_site', 'value' => '@tunahanakduhan', 'type' => 'text'],
            ['key' => 'twitter_creator', 'value' => '@tunahanakduhan', 'type' => 'text'],
            ['key' => 'twitter_title', 'value' => 'Tunahan Akduhan - Backend & API Developer', 'type' => 'text'],
            ['key' => 'twitter_description', 'value' => 'Laravel, Node.js, Vue.js ile backend development ve API geliştirme uzmanı.', 'type' => 'textarea'],
            ['key' => 'twitter_image', 'value' => '/images/twitter-card.jpg', 'type' => 'text'],
            
            // Schema.org
            ['key' => 'schema_type', 'value' => 'Person', 'type' => 'text'],
            ['key' => 'schema_name', 'value' => 'Tunahan Akduhan', 'type' => 'text'],
            ['key' => 'schema_job_title', 'value' => 'Backend & API Developer', 'type' => 'text'],
            ['key' => 'schema_url', 'value' => 'https://akduhan.com', 'type' => 'text'],
            ['key' => 'schema_same_as', 'value' => 'https://github.com/tunahanakduhan,https://linkedin.com/in/tunahanakduhan', 'type' => 'textarea'],
            
            // Technical SEO
            ['key' => 'canonical_url', 'value' => 'https://akduhan.com', 'type' => 'text'],
            ['key' => 'sitemap_url', 'value' => '/sitemap.xml', 'type' => 'text'],
            ['key' => 'robots_txt', 'value' => "User-agent: *\nAllow: /\nSitemap: https://akduhan.com/sitemap.xml", 'type' => 'textarea'],
            
            // Google Analytics & Search Console
            ['key' => 'google_site_verification', 'value' => '', 'type' => 'text'],
            ['key' => 'bing_site_verification', 'value' => '', 'type' => 'text'],
            ['key' => 'yandex_site_verification', 'value' => '', 'type' => 'text'],
            
            // Additional SEO
            ['key' => 'favicon_url', 'value' => '/favicon.ico', 'type' => 'text'],
            ['key' => 'apple_touch_icon', 'value' => '/images/apple-touch-icon.png', 'type' => 'text'],
            ['key' => 'theme_color', 'value' => '#3B82F6', 'type' => 'text'],
            ['key' => 'msapplication_tilecolor', 'value' => '#3B82F6', 'type' => 'text']
        ];

        foreach ($seoSettings as $setting) {
            SiteSetting::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type']
                ]
            );
        }

        echo "✅ SEO ayarları başarıyla eklendi!\n";
    }
}
