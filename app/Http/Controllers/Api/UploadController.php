<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Handle file upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120', // 5MB max
            'type' => 'required|string|in:cv,profile,image,document'
        ]);

        try {
            $file = $request->file('file');
            $type = $request->input('type');
            
            // Dosya türüne göre validation
            switch ($type) {
                case 'cv':
                    $request->validate([
                        'file' => 'mimes:pdf'
                    ]);
                    break;
                case 'profile':
                case 'image':
                    $request->validate([
                        'file' => 'mimes:jpeg,jpg,png,webp|max:2048' // 2MB for images
                    ]);
                    break;
                case 'document':
                    $request->validate([
                        'file' => 'mimes:pdf,doc,docx,txt'
                    ]);
                    break;
            }

            // Dosya adını oluştur
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug($originalName) . '_' . time() . '.' . $extension;
            
            // Dosya türüne göre klasör belirle
            $folder = 'uploads/' . $type;
            
            // Dosyayı kaydet
            $path = $file->storeAs($folder, $filename, 'public');
            
            // URL oluştur
            $url = Storage::url($path);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'type' => $file->getMimeType()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dosya yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete uploaded file
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string'
        ]);

        try {
            $path = $request->input('path');
            
            // URL'den path'i temizle
            $path = str_replace('/storage/', '', $path);
            
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Dosya başarıyla silindi.'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Dosya bulunamadı.'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dosya silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}