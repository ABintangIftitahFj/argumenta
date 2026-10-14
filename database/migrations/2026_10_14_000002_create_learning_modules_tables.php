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
        Schema::create('learning_modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedInteger('stage')->default(1);
            $table->unsignedInteger('order')->default(1);
            $table->text('description');
            $table->string('format', 20)->default('all');
            $table->string('difficulty', 30)->default('novice');
            $table->unsignedInteger('xp_reward')->default(100);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('user_learning_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_module_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('available');
            $table->unsignedInteger('score')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'learning_module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_learning_progress');
        Schema::dropIfExists('learning_modules');
    }
};
