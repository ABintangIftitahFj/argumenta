<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningModule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'stage',
        'order',
        'description',
        'format',
        'difficulty',
        'xp_reward',
        'is_published',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stage' => 'integer',
            'order' => 'integer',
            'xp_reward' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    /**
     * User progress entries for this module.
     */
    public function userProgress(): HasMany
    {
        return $this->hasMany(UserLearningProgress::class);
    }
}
