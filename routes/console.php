<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use App\Services\ImapService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Modern Laravel 12 syntax ile email sync command
Artisan::command('email:sync-direct {--force}', function (ImapService $imapService) {
    $this->info('Starting email synchronization...');
    
    try {
        $emails = $imapService->fetchNewEmails();
        $count = count($emails);
        
        if ($count > 0) {
            $this->info("Successfully synchronized {$count} new emails");
            foreach ($emails as $email) {
                $this->line("- {$email->name} ({$email->email}) - {$email->subject}");
            }
        } else {
            $this->info('No new emails found');
        }
        
        Log::info("Email sync completed successfully. {$count} emails processed.");
        
    } catch (\Exception $e) {
        $this->error('Email synchronization failed: ' . $e->getMessage());
        Log::error('Email sync failed', ['error' => $e->getMessage()]);
        return 1;
    }
    
    return 0;
})->purpose('Synchronize emails directly using IMAP service');

// Schedule configuration with modern Laravel 12 features
Schedule::command('emails:sync')
    ->everyTwoMinutes()
    ->withoutOverlapping(10) // 10 minutes overlap protection
    ->runInBackground()
    ->onFailure(function (\Throwable $exception) {
        Log::error('Scheduled email sync failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    })
    ->onSuccess(function () {
        Log::info('Scheduled email sync completed successfully');
    })
    ->description('Synchronize emails every 2 minutes');
