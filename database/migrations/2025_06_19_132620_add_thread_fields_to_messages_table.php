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
        Schema::table('messages', function (Blueprint $table) {
            $table->string('thread_id')->nullable()->after('subject');
            $table->enum('message_type', ['incoming', 'outgoing'])->default('incoming')->after('source');
            $table->timestamp('message_date')->nullable()->after('message_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['thread_id', 'message_type', 'message_date']);
        });
    }
};
