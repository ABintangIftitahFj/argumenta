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
        Schema::create('user_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('plan_tier', 20)->default('free'); // free, student, pro
            $table->unsignedInteger('total_credits')->default(5); // default 5 trial credits
            $table->unsignedInteger('used_credits')->default(0);
            $table->unsignedInteger('monthly_credit_limit')->default(5);
            $table->timestamp('reset_at')->nullable();
            $table->timestamps();

            $table->index(['plan_tier', 'reset_at']);
        });

        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('amount'); // negative for debit, positive for grant/refund
            $table->integer('balance_after');
            $table->enum('type', ['grant', 'reserve', 'settle', 'refund'])->default('settle');
            $table->string('description');
            $table->string('reference_id')->nullable(); // idempotency key or debate_session_id
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['reference_id']);
        });

        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('debate_session_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('feature', 50); // speech_evaluation, stt_transcription, coach_chat
            $table->string('provider', 50); // gemini, sumopod, mock_stt
            $table->string('model', 50); // gemini-1.5-pro, simulated_stt
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->decimal('cost_cents', 8, 4)->default(0.0000);
            $table->enum('status', ['success', 'quota_exceeded', 'provider_error', 'throttled'])->default('success');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'feature']);
            $table->index(['provider', 'model']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('credit_transactions');
        Schema::dropIfExists('user_credits');
    }
};
