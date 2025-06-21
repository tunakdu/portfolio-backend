<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;

class SEOController extends Controller
{
    public function index()
    {
        $seoKeys = [
            // Meta Tags
            'meta_description', 'meta_keywords', 'meta_author', 'meta_robots',
            // Open Graph
            'og_title', 'og_description', 'og_image', 'og_type', 'og_locale',
            // Twitter Card
            'twitter_card', 'twitter_site', 'twitter_creator', 'twitter_title', 'twitter_description', 'twitter_image',
            // Schema.org
            'schema_type', 'schema_name', 'schema_job_title', 'schema_url', 'schema_same_as',
            // Technical SEO
            'canonical_url', 'sitemap_url', 'robots_txt',
            // Verification
            'google_site_verification', 'bing_site_verification', 'yandex_site_verification',
            // Additional
            'favicon_url', 'apple_touch_icon', 'theme_color', 'msapplication_tilecolor'
        ];

        $seoSettings = SiteSetting::whereIn('key', $seoKeys)->get()->keyBy('key');
        
        // Vue.js için düz format'ta döndür
        $settings = [];
        foreach ($seoKeys as $key) {
            $settings[$key] = $seoSettings->has($key) ? $seoSettings[$key]->value : '';
        }

        return response()->json([
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'settings' => 'required|array'
        ]);

        foreach ($validatedData['settings'] as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value ?? '',
                    'type' => $this->getSettingType($key)
                ]
            );
        }

        return response()->json([
            'message' => 'SEO ayarları başarıyla güncellendi.'
        ]);
    }

    private function getSettingType($key)
    {
        $textareaFields = [
            'meta_description', 'og_description', 'twitter_description', 
            'schema_same_as', 'robots_txt'
        ];

        return in_array($key, $textareaFields) ? 'textarea' : 'text';
    }
}
