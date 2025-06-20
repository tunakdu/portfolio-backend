<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImapService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SyncEmailMessages extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:sync {--force : Force sync even if recently synced}';

    /**
     * The console command description.
     */
    protected $description = 'Synchronize emails from cPanel mailbox using IMAP';

    /**
     * Execute the console command.
     */
    public function handle(ImapService $imapService): int
    {
        $this->info('Starting email synchronization...');
        
        try {
            // Check last sync time with modern cache facade
            $lastSync = Cache::get('email_last_sync');
            $forceSync = $this->option('force');
            
            if (!$forceSync && $lastSync && $lastSync > now()->subMinutes(2)) {
                $this->warn('Last sync was less than 2 minutes ago. Use --force to override.');
                return self::SUCCESS;
            }
            
            $this->info('Connecting to IMAP server...');
            
            // Fetch new messages using dependency injection
            $newMessages = $imapService->fetchNewEmails();
            
            if (empty($newMessages)) {
                $this->info('No new messages found.');
            } else {
                $this->info(sprintf('Successfully synchronized %d new messages:', count($newMessages)));
                
                $this->newLine();
                foreach ($newMessages as $message) {
                    $this->line(sprintf('- %s (%s) - %s', 
                        $message->name, 
                        $message->email, 
                        $message->subject
                    ));
                }
            }
            
            // Cache last sync time with modern syntax
            Cache::put('email_last_sync', now(), 3600);
            
            $this->newLine();
            $this->info('Email synchronization completed successfully!');
            
            Log::info('Email synchronization successful', [
                'messages_count' => count($newMessages),
                'timestamp' => now()->toISOString()
            ]);
            
            return self::SUCCESS;
            
        } catch (\Throwable $e) {
            $this->error(sprintf('Email synchronization failed: %s', $e->getMessage()));
            
            Log::error('Email synchronization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);
            
            return self::FAILURE;
        }
    }
}