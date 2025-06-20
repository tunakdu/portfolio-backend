<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SEO ayarları zaten site_settings tablosunda key-value olarak saklanıyor
        // Bu migration sadece yeni SEO ayarlarını eklemek için
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SEO ayarları site_settings'den key'lere göre silinebilir
    }
};
