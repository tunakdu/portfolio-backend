<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ImapService;
use Illuminate\Support\Facades\Log;

class SyncEmailsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Job timeout süre (saniye)
     */
    public $timeout = 60;

    /**
     * Retry sayısı
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Email sync job başlatılıyor...');
            
            $imapService = new ImapService();
            $newMessages = $imapService->fetchNewEmails();
            
            if (count($newMessages) > 0) {
                Log::info(count($newMessages) . ' yeni email senkronize edildi.');
            } else {
                Log::info('Yeni email bulunamadı.');
            }
            
        } catch (\Exception $e) {
            Log::error('Email sync job hatası: ' . $e->getMessage());
            
            // Job'u tekrar kuyruğa almak için exception fırlat
            throw $e;
        }
    }
}
