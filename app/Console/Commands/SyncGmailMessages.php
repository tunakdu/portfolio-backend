<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImapService;
use Illuminate\Support\Facades\Log;

class SyncGmailMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gmail:sync {--force : Force sync even if recently synced}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gmail kutusundan yeni mesajlari ceker ve sisteme ekler';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Gmail mesajlari senkronize ediliyor...');
        
        try {
            // Son senkronizasyon zamanını kontrol et
            $lastSync = cache('gmail_last_sync');
            $forceSync = $this->option('force');
            
            if (!$forceSync && $lastSync && $lastSync > now()->subMinutes(5)) {
                $this->warn('Son senkronizasyon 5 dakikadan az bir sure once yapildi. --force kullanarak zorla senkronize edebilirsiniz.');
                return Command::SUCCESS;
            }
            
            $gmailService = new GmailService();
            
            // Access token'ı yenile
            if (!$gmailService->refreshAccessToken()) {
                $this->error('Gmail access token yenilenemedi. Lutfen OAuth ayarlarini kontrol edin.');
                return Command::FAILURE;
            }
            
            $this->info('Gmail API baglaniliyor...');
            
            // Yeni mesajlari cek
            $newMessages = $gmailService->fetchNewEmails();
            
            if (empty($newMessages)) {
                $this->info('Yeni mesaj bulunamadi.');
            } else {
                $this->info(count($newMessages) . ' yeni mesaj sisteme eklendi:');
                
                foreach ($newMessages as $message) {
                    $this->line('- ' . $message->name . ' (' . $message->email . ')');
                }
            }
            
            // Son senkronizasyon zamanını kaydet
            cache(['gmail_last_sync' => now()], 3600);
            
            $this->info('Gmail senkronizasyonu tamamlandi!');
            
            Log::info('Gmail senkronizasyonu basarili: ' . count($newMessages) . ' yeni mesaj');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Gmail senkronizasyon hatasi: ' . $e->getMessage());
            Log::error('Gmail senkronizasyon hatasi: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
