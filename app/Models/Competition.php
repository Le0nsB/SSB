<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'event_date',
        'registration_deadline',
        'team_limit',
        'entry_fee',
        'description',
        'is_published',
    ];

    protected $casts = [
        'event_date' => 'date',
        'registration_deadline' => 'date',
        'team_limit' => 'integer',
        'entry_fee' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(CompetitionTeam::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(CompetitionMatch::class);
    }
}
