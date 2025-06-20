<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Önce column var mı kontrol et
            if (!Schema::hasColumn('messages', 'message_id')) {
                $table->string('message_id')->nullable()->after('thread_id');
            }
            
            // Index'leri ekle (eğer yoksa)
            try {
                $table->index(['email', 'message_date'], 'idx_messages_email_date');
                $table->index(['thread_id', 'message_date'], 'idx_messages_thread_date');
                $table->index('message_id', 'idx_messages_message_id');
            } catch (\Exception $e) {
                // Index zaten varsa ignore et
            }
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('idx_messages_email_date');
            $table->dropIndex('idx_messages_thread_date'); 
            $table->dropIndex('idx_messages_message_id');
            $table->dropColumn('message_id');
        });
    }
};