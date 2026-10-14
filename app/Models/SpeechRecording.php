<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpeechRecording extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'debate_session_id',
        'user_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size_bytes',
        'duration_seconds',
        'upload_status',
        'storage_disk',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size_bytes' => 'integer',
            'duration_seconds' => 'integer',
        ];
    }

    /**
     * The debate session associated with this recording.
     */
    public function debateSession(): BelongsTo
    {
        return $this->belongsTo(DebateSession::class);
    }

    /**
     * The user that uploaded this recording.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
