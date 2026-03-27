<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'home_team_id',
        'away_team_id',
        'stage',
        'home_score',
        'away_score',
        'played_at',
    ];

    protected $casts = [
        'played_at' => 'datetime',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(CompetitionTeam::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(CompetitionTeam::class, 'away_team_id');
    }
}
