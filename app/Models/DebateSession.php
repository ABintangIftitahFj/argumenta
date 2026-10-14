<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebateSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'format',
        'motion_id',
        'motion_text',
        'role',
        'language',
        'difficulty',
        'duration_seconds',
        'poi_allowed',
        'poi_start_second',
        'poi_end_second',
        'status',
        'notes',
        'ai_coaching_enabled',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'poi_allowed' => 'boolean',
            'poi_start_second' => 'integer',
            'poi_end_second' => 'integer',
            'ai_coaching_enabled' => 'boolean',
        ];
    }

    /**
     * The user that owns the debate session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The motion associated with the session, if selected from catalog.
     */
    public function motion(): BelongsTo
    {
        return $this->belongsTo(Motion::class);
    }
}
