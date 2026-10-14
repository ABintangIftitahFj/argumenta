<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transcript extends Model
{
    use HasFactory;

    protected $fillable = [
        'speech_recording_id',
        'debate_session_id',
        'user_id',
        'raw_text',
        'user_edited_text',
        'language',
        'confidence_score',
        'words_count',
        'words_per_minute',
        'has_uncertainty',
        'audio_quality_warning',
        'asr_provider',
        'status',
    ];

    protected $casts = [
        'confidence_score' => 'float',
        'words_count' => 'integer',
        'words_per_minute' => 'integer',
        'has_uncertainty' => 'boolean',
    ];

    public function recording(): BelongsTo
    {
        return $this->belongsTo(SpeechRecording::class, 'speech_recording_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(DebateSession::class, 'debate_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function segments(): HasMany
    {
        return $this->hasMany(TranscriptSegment::class)->orderBy('segment_order');
    }

    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class);
    }

    /**
     * Get active text (user-edited if present, otherwise raw).
     */
    public function getEffectiveTextAttribute(): string
    {
        return $this->user_edited_text ?: $this->raw_text;
    }
}
