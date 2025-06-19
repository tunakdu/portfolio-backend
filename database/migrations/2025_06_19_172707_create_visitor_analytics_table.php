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
        Schema::create('visitor_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->string('page_url');
            $table->string('referrer')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('device_type', 50)->nullable(); // desktop, mobile, tablet
            $table->string('browser', 100)->nullable();
            $table->string('os', 100)->nullable();
            $table->integer('session_duration')->default(0); // seconds
            $table->json('utm_parameters')->nullable(); // utm_source, utm_medium, etc.
            $table->boolean('is_bot')->default(false);
            $table->string('session_id', 255)->nullable();
            $table->timestamp('visited_at');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['ip_address', 'visited_at']);
            $table->index(['page_url', 'visited_at']);
            $table->index(['visited_at']);
            $table->index(['session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_analytics');
    }
};
