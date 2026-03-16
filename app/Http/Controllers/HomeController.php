<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LoadsSsbMedia;
use App\Models\Competition;
use App\Models\NewsPost;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    use LoadsSsbMedia;

    public function index(): View
    {
        $nextCompetition = null;
        $latestFinishedCompetition = null;
        $latestNews = collect();
        $mediaItems = $this->getSsbMediaItems();

        if (Schema::hasTable('competitions')) {
            $nextCompetition = Competition::query()
                ->where('is_published', true)
                ->whereDate('event_date', '>=', now()->toDateString())
                ->orderBy('event_date')
                ->first();

            $latestFinishedCompetition = Competition::query()
                ->where('is_published', true)
                ->whereDate('event_date', '<', now()->toDateString())
                ->orderByDesc('event_date')
                ->first();

            if (
                $latestFinishedCompetition
                && Schema::hasTable('competition_teams')
                && Schema::hasTable('competition_matches')
            ) {
                $latestFinishedCompetition->load([
                    'matches' => fn ($query) => $query
                        ->with(['homeTeam', 'awayTeam'])
                        ->latest(),
                    'teams' => fn ($query) => $query
                        ->whereNotNull('final_position')
                        ->orderBy('final_position')
                        ->orderBy('name'),
                ]);
            }
        }

        if (Schema::hasTable('news_posts')) {
            $latestNews = NewsPost::query()
                ->where('is_published', true)
                ->orderByDesc('published_at')
                ->limit(3)
                ->get();
        }

        if (! $nextCompetition) {
            $nextCompetition = $this->fallbackNextCompetition();
        }

        if ($latestNews->isEmpty()) {
            $latestNews = $this->fallbackLatestNews();
        }

        $newsMediaByTitle = $this->mapMediaToTitles($mediaItems, [
            'Atvērta pieteikšanās Sunny Streetball #1' => 'video',
            'Publicēts turnīra dienas grafiks' => 'video',
            'Meklējam brīvprātīgos tiesnešus' => 'image',
        ]);

        return view('home', [
            'nextCompetition' => $nextCompetition,
            'latestFinishedCompetition' => $latestFinishedCompetition,
            'latestNews' => $latestNews,
            'mediaItems' => $mediaItems,
            'newsMediaByTitle' => $newsMediaByTitle,
        ]);
    }

    private function fallbackNextCompetition(): object
    {
        return (object) [
            'title' => 'Sunny Streetball #1',
            'location' => 'Priekuļu Sporta birze',
            'event_date' => Carbon::now()->addDays(12),
            'registration_deadline' => Carbon::now()->addDays(9),
            'team_limit' => 12,
            'entry_fee' => 5.00,
            'description' => 'Atklāšanas posms ar grupu turnīru un play-off.',
        ];
    }

    private function fallbackLatestNews(): \Illuminate\Support\Collection
    {
        return collect([
            (object) [
                'title' => 'Atvērta pieteikšanās Sunny Streetball #1',
                'excerpt' => 'Savāc komandu un piesakies līdz nākamās nedēļas trešdienai.',
            ],
            (object) [
                'title' => 'Publicēts turnīra dienas grafiks',
                'excerpt' => 'Komandu reģistrācija no 10:00, pirmās spēles starts 11:00.',
            ],
            (object) [
                'title' => 'Meklējam brīvprātīgos tiesnešus',
                'excerpt' => 'Ja vēlies iesaistīties pasākuma organizēšanā, dod mums ziņu Instagram.',
            ],
        ]);
    }
}
