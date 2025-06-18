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
