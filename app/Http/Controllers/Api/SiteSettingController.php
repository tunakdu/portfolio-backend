<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Tüm ayarları al ve 'key' => 'value' formatında bir koleksiyona dönüştür
        $settings = SiteSetting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'sometimes|string|max:50',
            'location' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string',
            'github' => 'sometimes|url',
            'linkedin' => 'sometimes|url',
            'twitter' => 'sometimes|url',
            'instagram' => 'sometimes|url',
            'siteTitle' => 'sometimes|string|max:255',
            'siteDescription' => 'sometimes|string',
            'keywords' => 'sometimes|string',
            'contactEmail' => 'sometimes|email|max:255',
            'contactPhone' => 'sometimes|string|max:50',
            'resumeUrl' => 'sometimes|url',
            'profileImage' => 'sometimes|url'
        ]);

        // For each validated field, update or create the corresponding SiteSetting
        foreach ($validated as $key => $value) {
            if ($value !== null && $value !== '') {
                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        return response()->json([
            'message' => 'Site settings updated successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
