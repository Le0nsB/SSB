<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionTeam;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    public function index(): View
    {
        $competitions = collect();
        $standingsByCompetition = [];
        $stageOptions = $this->stageOptions();

        if (Schema::hasTable('competitions')) {
            $competitions = Competition::query()
                ->where('is_published', true)
                ->orderByDesc('event_date')
                ->get();

            if (Schema::hasTable('competition_teams') && Schema::hasTable('competition_matches')) {
                $competitions->load([
                    'teams' => fn ($query) => $query
                        ->orderByRaw('final_position is null')
                        ->orderBy('final_position')
                        ->orderBy('name'),
                    'matches' => fn ($query) => $query
                        ->with(['homeTeam', 'awayTeam'])
                        ->orderByDesc('played_at')
                        ->latest(),
                ]);

                foreach ($competitions as $competition) {
                    $standingsByCompetition[$competition->id] = $this->buildStandings(
                        $competition->teams,
                        $competition->matches
                    );
                }
            }
        }

        if ($competitions->isEmpty()) {
            $competitions = collect([
                (object) [
                    'title' => 'Sunny Streetball #1',
                    'location' => 'Priekuļu Sporta birze',
                    'event_date' => Carbon::now()->addDays(12),
                    'registration_deadline' => Carbon::now()->addDays(9),
                    'description' => 'Atklāšanas posms ar 3x3 grupām un play-off līdz finālam.',
                ],
                (object) [
                    'title' => 'Sunny Streetball #2',
                    'location' => 'Cēsis, Pilsētas laukums',
                    'event_date' => Carbon::now()->addDays(26),
                    'registration_deadline' => Carbon::now()->addDays(22),
                    'description' => 'Vakara turnīrs ar mūziku un soda metienu konkursu skatītājiem.',
                ],
                (object) [
                    'title' => 'Sunny Streetball #3',
                    'location' => 'Priekuļi, Tehnikuma basketbola laukums',
                    'event_date' => Carbon::now()->addDays(40),
                    'registration_deadline' => Carbon::now()->addDays(36),
                    'description' => 'Sezonas noslēguma posms ar kopējo reitingu un balvām komandām.',
                ],
            ]);
        }

        return view('competitions.index', [
            'competitions' => $competitions,
            'standingsByCompetition' => $standingsByCompetition,
            'stageOptions' => $stageOptions,
        ]);
    }

    private function stageOptions(): array
    {
        return [
            'group' => 'Grupu spēle',
            'quarterfinal' => 'Ceturtdaļfināls',
            'semifinal' => 'Pusfināls',
            'final' => 'Fināls',
            'bronze' => 'Spēle par 3. vietu',
        ];
    }

    private function buildStandings(Collection $teams, Collection $matches): Collection
    {
        $table = $teams->mapWithKeys(function (CompetitionTeam $team): array {
            return [$team->id => [
                'team' => $team,
                'played' => 0,
                'wins' => 0,
                'losses' => 0,
                'points_for' => 0,
                'points_against' => 0,
                'diff' => 0,
                'table_points' => 0,
            ]];
        });

        foreach ($matches as $match) {
            if (! isset($table[$match->home_team_id], $table[$match->away_team_id])) {
                continue;
            }

            $home = $table[$match->home_team_id];
            $away = $table[$match->away_team_id];

            $home['played']++;
            $away['played']++;
            $home['points_for'] += $match->home_score;
            $home['points_against'] += $match->away_score;
            $away['points_for'] += $match->away_score;
            $away['points_against'] += $match->home_score;

            if ($match->home_score > $match->away_score) {
                $home['wins']++;
                $away['losses']++;
                $home['table_points'] += 2;
                $away['table_points'] += 1;
            } elseif ($match->away_score > $match->home_score) {
                $away['wins']++;
                $home['losses']++;
                $away['table_points'] += 2;
                $home['table_points'] += 1;
            } else {
                $home['table_points'] += 1;
                $away['table_points'] += 1;
            }

            $home['diff'] = $home['points_for'] - $home['points_against'];
            $away['diff'] = $away['points_for'] - $away['points_against'];

            $table[$match->home_team_id] = $home;
            $table[$match->away_team_id] = $away;
        }

        return $table
            ->values()
            ->sort(fn (array $a, array $b) => $this->compareStandingsRow($a, $b))
            ->values();
    }

    private function compareStandingsRow(array $a, array $b): int
    {
        if ($a['table_points'] !== $b['table_points']) {
            return $b['table_points'] <=> $a['table_points'];
        }

        if ($a['diff'] !== $b['diff']) {
            return $b['diff'] <=> $a['diff'];
        }

        if ($a['points_for'] !== $b['points_for']) {
            return $b['points_for'] <=> $a['points_for'];
        }

        return strcmp($a['team']->name, $b['team']->name);
    }
}
