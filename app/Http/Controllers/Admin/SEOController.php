<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;

class SEOController extends Controller
{
    public function index()
    {
        $seoSettings = SiteSetting::whereIn('key', [
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
        ])->get()->keyBy('key');

        return response()->json([
            'settings' => $seoSettings
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string'
        ]);

        foreach ($request->settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'] ?? '',
                    'type' => $this->getSettingType($setting['key'])
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
