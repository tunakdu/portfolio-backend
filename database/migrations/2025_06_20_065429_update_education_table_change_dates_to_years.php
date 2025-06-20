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
        Schema::table('education', function (Blueprint $table) {
            // Remove old columns
            $table->dropColumn(['start_date', 'end_date', 'description', 'location', 'website', 'gpa']);
            
            // Add new year columns
            $table->integer('start_year')->after('field_of_study');
            $table->integer('end_year')->nullable()->after('start_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('education', function (Blueprint $table) {
            // Remove year columns
            $table->dropColumn(['start_year', 'end_year']);
            
            // Add back old columns
            $table->date('start_date')->after('field_of_study');
            $table->date('end_date')->nullable()->after('start_date');
            $table->text('description')->nullable()->after('is_current');
            $table->string('location')->nullable()->after('description');
            $table->string('website')->nullable()->after('location');
            $table->decimal('gpa', 3, 2)->nullable()->after('website');
        });
    }
};