<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncEmailsJob;

class SyncEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'IMAP ile yeni e-postaları senkronize et';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Email senkronizasyonu başlatılıyor...');
        
        try {
            SyncEmailsJob::dispatch();
            $this->info('Email senkronizasyon job\'u kuyruğa eklendi.');
        } catch (\Exception $e) {
            $this->error('Hata: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
