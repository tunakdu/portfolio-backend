<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Article::where('is_published', true)
            ->latest('published_at')
            ->select('title', 'slug', 'cover_image', 'published_at', 'is_published')
            ->paginate(9);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'content' => 'required|string',
            'cover_image' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $slug = $request->slug ?: Str::slug($request->title);
        
        // Slug unique kontrolü
        $originalSlug = $slug;
        $counter = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $article = Article::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'cover_image' => $request->cover_image,
            'is_published' => $request->boolean('is_published', false),
            'published_at' => $request->boolean('is_published', false) ? now() : null,
        ]);

        return response()->json([
            'message' => 'Makale başarıyla oluşturuldu.',
            'article' => $article
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return $article;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'content' => 'required|string',
            'cover_image' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $slug = $request->slug ?: Str::slug($request->title);
        
        // Slug unique kontrolü (mevcut makale hariç)
        if ($slug !== $article->slug) {
            $originalSlug = $slug;
            $counter = 1;
            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Yayın durumu değişikliği
        $wasPublished = $article->is_published;
        $willBePublished = $request->boolean('is_published', false);
        
        $article->update([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'cover_image' => $request->cover_image,
            'is_published' => $willBePublished,
            'published_at' => $willBePublished && !$wasPublished ? now() : $article->published_at,
        ]);

        return response()->json([
            'message' => 'Makale başarıyla güncellendi.',
            'article' => $article
        ]);
    }

    /**
     * Upload cover image
     */
    public function uploadCoverImage(Request $request)
    {
        $request->validate([
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            
            // Store in public/storage/articles directory
            $path = $file->storeAs('articles', $filename, 'public');
            
            return response()->json([
                'message' => 'Görsel başarıyla yüklendi.',
                'path' => '/storage/' . $path,
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['message' => 'Görsel yüklenemedi.'], 400);
    }

    /**
     * Import article from Medium URL
     */
    public function importMedium(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        try {
            $url = $request->input('url');
            
            // Medium URL'sini kontrol et
            if (!str_contains($url, 'medium.com')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçerli bir Medium URL\'si giriniz.'
                ], 400);
            }

            // Medium makalesini çek
            $content = $this->fetchMediumContent($url);
            
            if (!$content) {
                return response()->json([
                    'success' => false,
                    'message' => 'Makale içeriği alınamadı.'
                ], 400);
            }

            // Makaleyi kaydet
            $article = Article::create([
                'title' => $content['title'],
                'slug' => Str::slug($content['title']),
                'content' => $content['content'],
                'is_published' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Makale başarıyla içe aktarıldı.',
                'article' => $article
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İçe aktarma sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import article from file
     */
    public function importFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:html,md,txt|max:10240', // 10MB
        ]);

        try {
            $file = $request->file('file');
            $content = file_get_contents($file->getRealPath());
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            
            // HTML içeriğini temizle
            if ($file->getClientOriginalExtension() === 'html') {
                $content = $this->cleanHtmlContent($content);
            }

            $article = Article::create([
                'title' => $fileName,
                'slug' => Str::slug($fileName),
                'content' => $content,
                'is_published' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dosya başarıyla içe aktarıldı.',
                'article' => $article
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dosya içe aktarılırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch content from Medium URL
     */
    private function fetchMediumContent($url)
    {
        try {
            // cURL ile içeriği çek
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $html = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || !$html) {
                return null;
            }

            // HTML'i parse et
            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            
            // Başlığı al
            $titleNodes = $dom->getElementsByTagName('title');
            $title = $titleNodes->length > 0 ? $titleNodes->item(0)->textContent : 'İsimsiz Makale';
            
            // Medium makale içeriğini al
            $xpath = new \DOMXPath($dom);
            $articleNodes = $xpath->query('//article | //main | //*[contains(@class, "postArticle")]');
            
            $content = '';
            if ($articleNodes->length > 0) {
                $content = $dom->saveHTML($articleNodes->item(0));
                $content = $this->cleanHtmlContent($content);
            }

            return [
                'title' => trim(str_replace(' - Medium', '', $title)),
                'content' => $content
            ];

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Clean HTML content from unnecessary tags while preserving formatting
     */
    private function cleanHtmlContent($html)
    {
        // Gereksiz HTML etiketlerini temizle ama içerik korumalı etiketleri koru
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);
        $html = preg_replace('/<nav\b[^<]*(?:(?!<\/nav>)<[^<]*)*<\/nav>/mi', '', $html);
        $html = preg_replace('/<header\b[^<]*(?:(?!<\/header>)<[^<]*)*<\/header>/mi', '', $html);
        $html = preg_replace('/<footer\b[^<]*(?:(?!<\/footer>)<[^<]*)*<\/footer>/mi', '', $html);
        
        // Medium'a özgü gereksiz div'leri ve wrapper'ları temizle ama içeriği koru
        $html = preg_replace('/<div[^>]*role="button"[^>]*>/i', '<div>', $html);
        $html = preg_replace('/<div[^>]*tabindex[^>]*>/i', '<div>', $html);
        
        // Sadece gereksiz class'ları temizle, kod blokları için gerekli olanları koru
        $html = preg_replace('/class="(?!language-|hljs)[^"]*"/i', '', $html);
        
        // Data attribute'ları temizle ama aria etiketlerini koru
        $html = preg_replace('/data-[^=]*="[^"]*"/i', '', $html);
        
        // Gereksiz span'ları temizle ama kod içindekileri koru
        $html = preg_replace('/<span\s*>/i', '', $html);
        $html = preg_replace('/<\/span>/i', '', $html);
        
        // Boş div'leri temizle
        $html = preg_replace('/<div[^>]*>\s*<\/div>/i', '', $html);
        
        // Medium'un resim wrapper'larını temizle ama img etiketlerini koru
        $html = preg_replace('/<figure[^>]*>/i', '<figure>', $html);
        $html = preg_replace('/<picture[^>]*>/i', '<picture>', $html);
        $html = preg_replace('/<source[^>]*>/i', '<source>', $html);
        
        // Pre ve code etiketlerini koru
        $html = preg_replace('/<pre[^>]*>/i', '<pre>', $html);
        $html = preg_replace('/<code[^>]*>/i', '<code>', $html);
        
        // H1, H2, H3 etiketlerini temizle ama koru
        $html = preg_replace('/<(h[1-6])[^>]*>/i', '<$1>', $html);
        
        // P etiketlerini temizle ama koru
        $html = preg_replace('/<p[^>]*>/i', '<p>', $html);
        
        // Lista etiketlerini koru
        $html = preg_replace('/<(ul|ol|li)[^>]*>/i', '<$1>', $html);
        
        // Link etiketlerini temizle ama href'i koru
        $html = preg_replace('/<a[^>]*href="([^"]*)"[^>]*>/i', '<a href="$1">', $html);
        
        // Img etiketlerini temizle ama src, alt, width, height'i koru
        $html = preg_replace('/<img[^>]*src="([^"]*)"[^>]*(?:alt="([^"]*)")?[^>]*(?:width="([^"]*)")?[^>]*(?:height="([^"]*)")?[^>]*>/i', 
                           '<img src="$1" alt="$2" width="$3" height="$4">', $html);
        
        // Fazla boşlukları temizle
        $html = preg_replace('/\s+/', ' ', $html);
        $html = preg_replace('/\n\s*\n/', "\n\n", $html);
        
        return trim($html);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        // Delete cover image if exists
        if ($article->cover_image && str_starts_with($article->cover_image, '/storage/')) {
            $imagePath = str_replace('/storage/', '', $article->cover_image);
            Storage::disk('public')->delete($imagePath);
        }
        
        $article->delete();
        return response()->json(null, 204);
    }
}
