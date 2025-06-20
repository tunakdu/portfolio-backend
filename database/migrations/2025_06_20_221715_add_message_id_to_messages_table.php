<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('message_id')->nullable()->unique()->after('thread_id');
            $table->index(['email', 'message_date']);
            $table->index(['thread_id', 'message_date']);
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('message_id');
            $table->dropIndex(['email', 'message_date']);
            $table->dropIndex(['thread_id', 'message_date']);
        });
    }
};