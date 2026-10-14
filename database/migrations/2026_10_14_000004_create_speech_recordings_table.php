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
        Schema::create('speech_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debate_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type', 50);
            $table->unsignedBigInteger('file_size_bytes');
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->string('upload_status', 30)->default('uploaded');
            $table->string('storage_disk', 30)->default('private');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speech_recordings');
    }
};
