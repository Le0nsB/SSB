<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'name',
        'final_position',
    ];

    protected $casts = [
        'final_position' => 'integer',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(CompetitionMatch::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(CompetitionMatch::class, 'away_team_id');
    }
}
