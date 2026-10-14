<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'debate_session_id',
        'transcript_id',
        'user_id',
        'debate_format',
        'role',
        'overall_score',
        'matter_score',
        'manner_score',
        'method_score',
        'language_score',
        'summary',
        'strengths',
        'improvements',
        'next_drill_recommendation',
        'model_version',
        'rubric_version',
        'prompt_version',
        'status',
    ];

    protected $casts = [
        'overall_score' => 'float',
        'matter_score' => 'float',
        'manner_score' => 'float',
        'method_score' => 'float',
        'language_score' => 'float',
        'strengths' => 'array',
        'improvements' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(DebateSession::class, 'debate_session_id');
    }

    public function transcript(): BelongsTo
    {
        return $this->belongsTo(Transcript::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(EvaluationIssue::class);
    }
}
