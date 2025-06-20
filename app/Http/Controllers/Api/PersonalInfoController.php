<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PersonalInfo;
use Illuminate\Support\Facades\Validator;

class PersonalInfoController extends Controller
{
    /**
     * Tüm kişisel bilgileri getir
     */
    public function index()
    {
        $personalInfo = PersonalInfo::all()->groupBy('category');
        
        return response()->json([
            'data' => $personalInfo,
            'flat' => PersonalInfo::getAllKeyValue()
        ]);
    }

    /**
     * Belirli bir kategoriyi getir
     */
    public function getByCategory($category)
    {
        // SEO kategorisi için SiteSetting'den çek
        if ($category === 'seo') {
            $seoSettings = \App\Models\SiteSetting::whereIn('key', [
                'meta_description', 'meta_keywords', 'meta_author', 'meta_robots',
                'og_title', 'og_description', 'og_image', 'og_type', 'og_locale',
                'twitter_card', 'twitter_site', 'twitter_creator', 'twitter_title', 'twitter_description', 'twitter_image',
                'schema_type', 'schema_name', 'schema_job_title', 'schema_url', 'schema_same_as',
                'canonical_url', 'sitemap_url', 'robots_txt',
                'google_site_verification', 'bing_site_verification', 'yandex_site_verification',
                'favicon_url', 'apple_touch_icon', 'theme_color', 'msapplication_tilecolor'
            ])->get();
            
            return response()->json([
                'category' => $category,
                'settings' => $seoSettings
            ]);
        }
        
        $data = PersonalInfo::getByCategory($category);
        
        return response()->json([
            'category' => $category,
            'data' => $data
        ]);
    }

    /**
     * Belirli bir key'i getir
     */
    public function getByKey($key)
    {
        $value = PersonalInfo::getValue($key);
        
        if ($value === null) {
            return response()->json([
                'message' => 'Key not found'
            ], 404);
        }

        return response()->json([
            'key' => $key,
            'value' => $value
        ]);
    }

    /**
     * Kişisel bilgileri güncelle
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.key' => 'required|string',
            'data.*.value' => 'required',
            'data.*.type' => 'sometimes|string|in:text,email,phone,url,json,date',
            'data.*.category' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->data as $item) {
            PersonalInfo::setValue(
                $item['key'],
                $item['value'],
                $item['type'] ?? 'text',
                $item['category'] ?? null
            );
        }

        return response()->json([
            'message' => 'Personal information updated successfully'
        ]);
    }

    /**
     * Tek bir key'i güncelle
     */
    public function updateKey(Request $request, $key)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required',
            'type' => 'sometimes|string|in:text,email,phone,url,json,date',
            'category' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        PersonalInfo::setValue(
            $key,
            $request->value,
            $request->type ?? 'text',
            $request->category ?? null
        );

        return response()->json([
            'message' => 'Key updated successfully',
            'key' => $key,
            'value' => $request->value
        ]);
    }

    /**
     * Public endpoint - sadece gerekli bilgileri döner
     */
    public function public()
    {
        $publicData = PersonalInfo::whereIn('category', ['about', 'contact', 'social', 'site', 'files'])
            ->get()
            ->groupBy('category');

        // Skills'i decode et
        if (isset($publicData['skills'])) {
            foreach ($publicData['skills'] as $skill) {
                if ($skill->type === 'json') {
                    $skill->value = json_decode($skill->value, true);
                }
            }
        }

        return response()->json([
            'data' => $publicData,
            'flat' => PersonalInfo::whereIn('category', ['about', 'contact', 'social', 'site', 'files'])
                ->get()
                ->pluck('value', 'key')
        ]);
    }
}
