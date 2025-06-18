<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImapService;
use Illuminate\Support\Facades\Log;

class SyncEmailMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:sync {--force : Force sync even if recently synced}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cPanel mail kutusundan yeni mesajlari ceker ve sisteme ekler';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('E-posta mesajlari senkronize ediliyor...');
        
        try {
            // Son senkronizasyon zamanını kontrol et
            $lastSync = cache('email_last_sync');
            $forceSync = $this->option('force');
            
            if (!$forceSync && $lastSync && $lastSync > now()->subMinutes(2)) {
                $this->warn('Son senkronizasyon 2 dakikadan az bir sure once yapildi. --force kullanarak zorla senkronize edebilirsiniz.');
                return Command::SUCCESS;
            }
            
            $imapService = new ImapService();
            
            $this->info('IMAP sunucusuna baglaniliyor...');
            
            // Yeni mesajlari cek
            $newMessages = $imapService->fetchNewEmails();
            
            if (empty($newMessages)) {
                $this->info('Yeni mesaj bulunamadi.');
            } else {
                $this->info(count($newMessages) . ' yeni mesaj sisteme eklendi:');
                
                foreach ($newMessages as $message) {
                    $this->line('- ' . $message->name . ' (' . $message->email . ') - ' . $message->subject);
                }
            }
            
            // Son senkronizasyon zamanını kaydet
            cache(['email_last_sync' => now()], 3600);
            
            $this->info('E-posta senkronizasyonu tamamlandi!');
            
            Log::info('E-posta senkronizasyonu basarili: ' . count($newMessages) . ' yeni mesaj');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('E-posta senkronizasyon hatasi: ' . $e->getMessage());
            Log::error('E-posta senkronizasyon hatasi: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}