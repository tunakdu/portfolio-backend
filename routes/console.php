<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Gmail mesajlarını her 2 dakikada bir senkronize et
Schedule::command('gmail:sync')
    ->everyTwoMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onFailure(function () {
        \Log::error('Gmail senkronizasyon zamanlı görevi başarısız');
    })
    ->onSuccess(function () {
        \Log::info('Gmail senkronizasyon zamanlı görevi başarılı');
    });
