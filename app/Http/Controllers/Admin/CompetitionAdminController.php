<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\CompetitionTeam;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompetitionAdminController extends Controller
{
    public function index(): View
    {
        $competitions = Competition::query()->orderByDesc('event_date')->get();

        return view('admin.competitions.index', compact('competitions'));
    }

    public function create(): View
    {
        return view('admin.competitions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Competition::create($this->validatedCompetitionData($request));

        return redirect()->route('admin.competitions.index');
    }

    public function edit(Competition $competition): View
    {
        $this->loadCompetitionRelations($competition);

        $savedTeams = Team::query()->orderBy('name')->get();
        $stageOptions = $this->stageOptions();
        $standings = $this->buildStandings($competition->teams, $competition->matches);
        $nextMatchStage = $competition->teams->count() >= 2
            ? ($stageOptions[$this->inferMatchStage($competition)] ?? null)
            : null;

        return view('admin.competitions.edit', compact('competition', 'savedTeams', 'stageOptions', 'standings', 'nextMatchStage'));
    }

    public function update(Request $request, Competition $competition): RedirectResponse
    {
        $competition->update($this->validatedCompetitionData($request));

        return redirect()->route('admin.competitions.index');
    }

    public function destroy(Competition $competition): RedirectResponse
    {
        $competition->delete();

        return redirect()->route('admin.competitions.index');
    }

    public function storeTeam(Request $request, Competition $competition): RedirectResponse
    {
        $data = $request->validate([
            'saved_team_id' => ['nullable', 'integer', Rule::exists('teams', 'id')],
            'name' => ['nullable', 'string', 'max:255', 'required_without:saved_team_id'],
        ]);

        $teamName = $this->resolveTeamName($data);

        if (is_string($teamName)) {
            $teamName = preg_replace('/\s+/u', ' ', trim($teamName));
        }

        if (! $teamName) {
            return back()->withErrors(['name' => 'Norādi komandas nosaukumu vai izvēlies saglabātu komandu.']);
        }

        if ($competition->team_limit && $competition->teams()->count() >= $competition->team_limit) {
            return back()->withErrors(['team' => 'Sasniegts maksimālais komandu skaits šīm sacensībām.']);
        }

        $alreadyExists = $competition->teams()
            ->whereRaw('lower(trim(name)) = ?', [mb_strtolower($teamName)])
            ->exists();

        if ($alreadyExists) {
            return back()->withErrors(['name' => 'Šāda komanda šīm sacensībām jau ir pievienota.']);
        }

        CompetitionTeam::create([
            'competition_id' => $competition->id,
            'name' => $teamName,
        ]);

        return redirect()->route('admin.competitions.edit', $competition);
    }

    public function storeMatch(Request $request, Competition $competition): RedirectResponse
    {
        $data = $request->validate([
            'home_team_id' => [
                'required',
                'integer',
                Rule::exists('competition_teams', 'id')->where('competition_id', $competition->id),
            ],
            'away_team_id' => [
                'required',
                'integer',
                'different:home_team_id',
                Rule::exists('competition_teams', 'id')->where('competition_id', $competition->id),
            ],
            'home_score' => ['required', 'integer', 'min:0', 'max:300'],
            'away_score' => ['required', 'integer', 'min:0', 'max:300'],
            'played_at' => ['nullable', 'date'],
        ]);

        $autoStage = $this->inferMatchStage($competition);

        CompetitionMatch::create([
            'competition_id' => $competition->id,
            'home_team_id' => $data['home_team_id'],
            'away_team_id' => $data['away_team_id'],
            'stage' => $autoStage,
            'home_score' => $data['home_score'],
            'away_score' => $data['away_score'],
            'played_at' => $data['played_at'] ?? null,
        ]);

        return redirect()->route('admin.competitions.edit', $competition);
    }

    public function updatePlacements(Request $request, Competition $competition): RedirectResponse
    {
        $data = $request->validate([
            'positions' => ['nullable', 'array'],
            'positions.*' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        $positions = collect($data['positions'] ?? []);

        $competition->teams()->each(function (CompetitionTeam $team) use ($positions): void {
            $position = $positions->get((string) $team->id);

            $team->update([
                'final_position' => $position !== null && $position !== '' ? (int) $position : null,
            ]);
        });

        return redirect()->route('admin.competitions.edit', $competition);
    }

    private function competitionRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'registration_deadline' => ['nullable', 'date'],
            'team_limit' => ['nullable', 'integer', 'min:1', 'max:999'],
            'entry_fee' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'description' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    private function validatedCompetitionData(Request $request): array
    {
        $data = $request->validate($this->competitionRules());
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }

    private function loadCompetitionRelations(Competition $competition): void
    {
        $competition->load([
            'teams' => fn ($query) => $query->orderBy('name'),
            'matches' => fn ($query) => $query
                ->with(['homeTeam', 'awayTeam'])
                ->orderByDesc('played_at')
                ->latest(),
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

    private function resolveTeamName(array $data): ?string
    {
        if (! empty($data['saved_team_id'])) {
            return Team::query()->find($data['saved_team_id'])?->name;
        }

        return $data['name'] ?? null;
    }

    private function inferMatchStage(Competition $competition): string
    {
        $teamCount = $competition->teams()->count();
        $matchCount = $competition->matches()->count();

        if ($teamCount <= 2) {
            return 'final';
        }

        // Classic 4-team streetball bracket: 2x semifinal, bronze game, final.
        if ($teamCount === 4) {
            if ($matchCount < 2) {
                return 'semifinal';
            }

            return $matchCount === 2 ? 'bronze' : 'final';
        }

        // Classic 8-team bracket: 4x quarterfinal, 2x semifinal, bronze game, final.
        if ($teamCount === 8) {
            if ($matchCount < 4) {
                return 'quarterfinal';
            }

            if ($matchCount < 6) {
                return 'semifinal';
            }

            return $matchCount === 6 ? 'bronze' : 'final';
        }

        // For other team counts, default to group games to keep setup simple.
        return 'group';
    }
}
