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
        Schema::create('transcripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('speech_recording_id')->constrained('speech_recordings')->onDelete('cascade');
            $table->foreignId('debate_session_id')->constrained('debate_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->longText('raw_text');
            $table->longText('user_edited_text')->nullable();
            $table->string('language', 10)->default('id');
            $table->decimal('confidence_score', 4, 3)->default(0.950);
            $table->unsignedInteger('words_count')->default(0);
            $table->unsignedInteger('words_per_minute')->default(0);
            $table->boolean('has_uncertainty')->default(false);
            $table->string('audio_quality_warning')->nullable();
            $table->string('asr_provider', 50)->default('mock_stt');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('completed');
            $table->timestamps();

            $table->index(['debate_session_id', 'status']);
        });

        Schema::create('transcript_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transcript_id')->constrained('transcripts')->onDelete('cascade');
            $table->unsignedInteger('segment_order');
            $table->decimal('start_time_seconds', 6, 2);
            $table->decimal('end_time_seconds', 6, 2);
            $table->text('text');
            $table->decimal('confidence', 4, 3)->default(0.950);
            $table->boolean('is_uncertain')->default(false);
            $table->timestamps();

            $table->index(['transcript_id', 'segment_order']);
        });

        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debate_session_id')->unique()->constrained('debate_sessions')->onDelete('cascade');
            $table->foreignId('transcript_id')->constrained('transcripts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('debate_format', 10);
            $table->string('role', 50);
            $table->decimal('overall_score', 4, 1);
            $table->decimal('matter_score', 4, 1);
            $table->decimal('manner_score', 4, 1);
            $table->decimal('method_score', 4, 1);
            $table->decimal('language_score', 4, 1)->nullable();
            $table->text('summary');
            $table->json('strengths');
            $table->json('improvements');
            $table->string('next_drill_recommendation');
            $table->string('model_version', 50)->default('gemini-1.5-pro');
            $table->string('rubric_version', 50)->default('wudc-2024');
            $table->string('prompt_version', 50)->default('v1.0');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('completed');
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('evaluation_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->string('category', 50); // matter, manner, method, language, logic_fallacy, arel
            $table->string('title');
            $table->text('original_quote')->nullable();
            $table->text('suggested_fix')->nullable();
            $table->text('explanation');
            $table->decimal('start_time_seconds', 6, 2)->nullable();
            $table->decimal('end_time_seconds', 6, 2)->nullable();
            $table->enum('severity', ['minor', 'moderate', 'critical'])->default('moderate');
            $table->timestamps();

            $table->index(['evaluation_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_issues');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('transcript_segments');
        Schema::dropIfExists('transcripts');
    }
};
