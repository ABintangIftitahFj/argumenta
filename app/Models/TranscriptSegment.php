<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranscriptSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transcript_id',
        'segment_order',
        'start_time_seconds',
        'end_time_seconds',
        'text',
        'confidence',
        'is_uncertain',
    ];

    protected $casts = [
        'segment_order' => 'integer',
        'start_time_seconds' => 'float',
        'end_time_seconds' => 'float',
        'confidence' => 'float',
        'is_uncertain' => 'boolean',
    ];

    public function transcript(): BelongsTo
    {
        return $this->belongsTo(Transcript::class);
    }
}
