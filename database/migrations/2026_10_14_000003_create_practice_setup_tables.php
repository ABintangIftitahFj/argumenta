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
        Schema::create('motions', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('info_slide')->nullable();
            $table->string('theme', 50)->default('Umum');
            $table->string('difficulty', 30)->default('novice');
            $table->string('language', 10)->default('id');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('debate_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('format', 20)->default('BP');
            $table->foreignId('motion_id')->nullable()->constrained('motions')->nullOnDelete();
            $table->text('motion_text');
            $table->string('role', 30);
            $table->string('language', 10)->default('id');
            $table->string('difficulty', 30)->default('novice');
            $table->unsignedInteger('duration_seconds')->default(420);
            $table->boolean('poi_allowed')->default(true);
            $table->unsignedInteger('poi_start_second')->default(60);
            $table->unsignedInteger('poi_end_second')->default(360);
            $table->string('status', 30)->default('setup');
            $table->text('notes')->nullable();
            $table->boolean('ai_coaching_enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debate_sessions');
        Schema::dropIfExists('motions');
    }
};
