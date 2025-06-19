<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use Illuminate\Support\Str;

class BulkImportMediumArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:medium-bulk {--force : Force reimport existing articles}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk import all Medium articles from predefined list';

    /**
     * Medium article URLs to import (newest to oldest)
     */
    private array $articleUrls = [
        // En yeni makaleler (en üstte gözükecek)
        'https://medium.com/@akduhant/laravel-scoute-ve-meilisearch-b8c1f69117d4',
        'https://medium.com/@akduhant/laravelde-mutator-ve-accessor-kullan%C4%B1m%C4%B1-verileriniz-%C3%BCzerinde-daha-fazla-kontrol-sa%C4%9Flay%C4%B1n-3099ad32e1d6',
        'https://medium.com/@akduhant/laravelde-i%CC%87li%C5%9Fkiler-relationships-ve-%C3%B6rneklerle-anlat%C4%B1m%C4%B1-f0fe96057dad',
        'https://medium.com/@akduhant/laravel-11-%C3%BCzerinde-basemodel-ve-baserepository-mant%C4%B1%C4%9F%C4%B1-a3dadda346f5',
        // En eski makale (en altta gözükecek)
        'https://medium.com/@akduhant/php-crud-operations-faa8e9e704af',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🚀 Medium makalelerinin bulk import işlemi başlıyor...");
        $this->info("📊 Toplam " . count($this->articleUrls) . " makale import edilecek.\n");

        $successCount = 0;
        $skipCount = 0;
        $errorCount = 0;
        $force = $this->option('force');

        foreach ($this->articleUrls as $index => $url) {
            $this->line("🔄 [" . ($index + 1) . "/" . count($this->articleUrls) . "] Import ediliyor: " . $url);

            try {
                // URL'den başlık tahmin et (slug kontrolü için)
                $urlParts = parse_url($url);
                $pathParts = explode('/', trim($urlParts['path'], '/'));
                $lastPart = end($pathParts);
                $estimatedSlug = Str::slug($lastPart);

                // Eğer force değilse ve makale zaten varsa atla
                if (!$force && Article::where('slug', 'like', '%' . substr($estimatedSlug, 0, 50) . '%')->exists()) {
                    $this->warn("⚠️  Makale zaten mevcut, atlanıyor. (--force ile yeniden import edebilirsiniz)");
                    $skipCount++;
                    continue;
                }

                // Medium makalesini çek
                $content = $this->fetchMediumContent($url);
                
                if (!$content) {
                    $this->error("❌ Makale içeriği alınamadı: " . $url);
                    $errorCount++;
                    continue;
                }

                // Slug kontrolü
                $slug = Str::slug($content['title']);
                $originalSlug = $slug;
                $counter = 1;
                while (Article::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                // Makaleyi kaydet (yeni makaleler daha geç tarihle kaydedilir)
                $publishedDate = now()->subDays(count($this->articleUrls) - $index - 1);
                
                $article = Article::create([
                    'title' => $content['title'],
                    'slug' => $slug,
                    'content' => $content['content'],
                    'is_published' => false, // Başlangıçta draft olarak kaydet
                    'published_at' => $publishedDate,
                ]);

                $this->info("✅ Başarılı: " . $content['title'] . " (ID: {$article->id})");
                $successCount++;

            } catch (\Exception $e) {
                $this->error("❌ Hata oluştu: " . $e->getMessage());
                $errorCount++;
            }

            // Her makale arasında kısa bir bekleme
            usleep(500000); // 0.5 saniye
        }

        $this->line("\n" . str_repeat("=", 60));
        $this->info("📋 BULK IMPORT SONUCU:");
        $this->info("✅ Başarılı: " . $successCount);
        $this->warn("⚠️  Atlanan: " . $skipCount);
        $this->error("❌ Hatalı: " . $errorCount);
        $this->info("📊 Toplam: " . count($this->articleUrls));
        $this->line(str_repeat("=", 60));

        if ($successCount > 0) {
            $this->info("\n🎉 Import işlemi tamamlandı! Admin panelinden makaleleri kontrol edebilirsiniz.");
        }

        return 0;
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

            if (empty($content)) {
                return null;
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
        // ArticleController'daki aynı temizleme fonksiyonu
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);
        $html = preg_replace('/<nav\b[^<]*(?:(?!<\/nav>)<[^<]*)*<\/nav>/mi', '', $html);
        $html = preg_replace('/<header\b[^<]*(?:(?!<\/header>)<[^<]*)*<\/header>/mi', '', $html);
        $html = preg_replace('/<footer\b[^<]*(?:(?!<\/footer>)<[^<]*)*<\/footer>/mi', '', $html);
        
        $html = preg_replace('/<div[^>]*role="button"[^>]*>/i', '<div>', $html);
        $html = preg_replace('/<div[^>]*tabindex[^>]*>/i', '<div>', $html);
        $html = preg_replace('/class="(?!language-|hljs)[^"]*"/i', '', $html);
        $html = preg_replace('/data-[^=]*="[^"]*"/i', '', $html);
        $html = preg_replace('/<span\s*>/i', '', $html);
        $html = preg_replace('/<\/span>/i', '', $html);
        $html = preg_replace('/<div[^>]*>\s*<\/div>/i', '', $html);
        $html = preg_replace('/<figure[^>]*>/i', '<figure>', $html);
        $html = preg_replace('/<picture[^>]*>/i', '<picture>', $html);
        $html = preg_replace('/<source[^>]*>/i', '<source>', $html);
        $html = preg_replace('/<pre[^>]*>/i', '<pre>', $html);
        $html = preg_replace('/<code[^>]*>/i', '<code>', $html);
        $html = preg_replace('/<(h[1-6])[^>]*>/i', '<$1>', $html);
        $html = preg_replace('/<p[^>]*>/i', '<p>', $html);
        $html = preg_replace('/<(ul|ol|li)[^>]*>/i', '<$1>', $html);
        $html = preg_replace('/<a[^>]*href="([^"]*)"[^>]*>/i', '<a href="$1">', $html);
        $html = preg_replace('/<img[^>]*src="([^"]*)"[^>]*(?:alt="([^"]*)")?[^>]*(?:width="([^"]*)")?[^>]*(?:height="([^"]*)")?[^>]*>/i', 
                           '<img src="$1" alt="$2" width="$3" height="$4">', $html);
        
        $html = preg_replace('/\s+/', ' ', $html);
        $html = preg_replace('/\n\s*\n/', "\n\n", $html);
        
        return trim($html);
    }
}
