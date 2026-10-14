<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'category',
        'title',
        'original_quote',
        'suggested_fix',
        'explanation',
        'start_time_seconds',
        'end_time_seconds',
        'severity',
    ];

    protected $casts = [
        'start_time_seconds' => 'float',
        'end_time_seconds' => 'float',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }
}
