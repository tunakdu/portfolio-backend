<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImapService;
use Illuminate\Support\Facades\Log;

class SyncEmails extends Command
{
    protected $signature = 'emails:sync';
    protected $description = 'IMAP ile yeni e-postaları senkronize et';

    public function handle()
    {
        $this->info('Email senkronizasyonu başlatılıyor...');

        try {
            // Direct sync yapalım, job'a gerek yok
            $imapService = new ImapService();
            $newMessages = $imapService->fetchNewEmails();

            if (count($newMessages) > 0) {
                $this->info(count($newMessages) . ' yeni email senkronize edildi.');
                Log::info(count($newMessages) . ' yeni email senkronize edildi.');
            } else {
                $this->info('Yeni email bulunamadı.');
                Log::info('Yeni email bulunamadı.');
            }

        } catch (\Exception $e) {
            $this->error('Hata: ' . $e->getMessage());
            Log::error('Email sync hatası: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}