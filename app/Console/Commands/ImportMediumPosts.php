<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use Illuminate\Support\Str;

class ImportMediumPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:medium {url} {--category=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import articles from Medium URL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = $this->argument('url');
        $categoryId = $this->option('category');

        $this->info("Medium makale import ediliyor: {$url}");

        try {
            // Medium URL'sini kontrol et
            if (!str_contains($url, 'medium.com')) {
                $this->error('Geçerli bir Medium URL\'si giriniz.');
                return 1;
            }

            // Medium makalesini çek
            $content = $this->fetchMediumContent($url);
            
            if (!$content) {
                $this->error('Makale içeriği alınamadı.');
                return 1;
            }

            // Slug kontrolü
            $slug = Str::slug($content['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (Article::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Makaleyi kaydet
            $article = Article::create([
                'title' => $content['title'],
                'slug' => $slug,
                'content' => $content['content'],
                'is_published' => false,
                'category_id' => $categoryId,
            ]);

            $this->info("✅ Makale başarıyla import edildi: {$article->title}");
            $this->line("   ID: {$article->id}");
            $this->line("   Slug: {$article->slug}");
            
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Import hatası: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Fetch content from Medium URL
     */
    private function fetchMediumContent($url)
    {
        try {
            $this->line("🔄 İçerik çekiliyor...");
            
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
                $this->error("HTTP Error: {$httpCode}");
                return null;
            }

            $this->line("📄 HTML parse ediliyor...");

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
                $this->error("Makale içeriği bulunamadı.");
                return null;
            }

            $cleanTitle = trim(str_replace(' - Medium', '', $title));
            $this->line("📝 Başlık: {$cleanTitle}");
            $this->line("📊 İçerik uzunluğu: " . strlen($content) . " karakter");

            return [
                'title' => $cleanTitle,
                'content' => $content
            ];

        } catch (\Exception $e) {
            $this->error("Parse hatası: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Clean HTML content from unnecessary tags
     */
    private function cleanHtmlContent($html)
    {
        $this->line("🧹 HTML temizleniyor...");
        
        // Gereksiz HTML etiketlerini temizle
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);
        $html = preg_replace('/<nav\b[^<]*(?:(?!<\/nav>)<[^<]*)*<\/nav>/mi', '', $html);
        $html = preg_replace('/<header\b[^<]*(?:(?!<\/header>)<[^<]*)*<\/header>/mi', '', $html);
        $html = preg_replace('/<footer\b[^<]*(?:(?!<\/footer>)<[^<]*)*<\/footer>/mi', '', $html);
        
        // Medium'a özgü class'ları temizle
        $html = preg_replace('/class="[^"]*"/i', '', $html);
        $html = preg_replace('/data-[^=]*="[^"]*"/i', '', $html);
        
        // Boş satırları temizle
        $html = preg_replace('/\n\s*\n/', "\n", $html);
        
        return trim($html);
    }
}
