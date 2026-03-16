<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    public function index(): View
    {
        $competitions = collect();

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
                        ->latest(),
                ]);
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
        ]);
    }
}
