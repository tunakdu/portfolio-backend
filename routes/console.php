<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// E-posta mesajlarını her 2 dakikada bir senkronize et
Schedule::command('emails:sync')
    ->everyTwoMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onFailure(function () {
        \Log::error('E-posta senkronizasyon zamanlı görevi başarısız');
    })
    ->onSuccess(function () {
        \Log::info('E-posta senkronizasyon zamanlı görevi başarılı');
    });
