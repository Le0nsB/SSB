<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\CompetitionTeam;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'registration_deadline' => ['nullable', 'date'],
            'team_limit' => ['nullable', 'integer', 'min:1', 'max:999'],
            'entry_fee' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'description' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        Competition::create($data);

        return redirect()->route('admin.competitions.index');
    }

    public function edit(Competition $competition): View
    {
        $competition->load([
            'teams' => fn ($query) => $query->orderBy('name'),
            'matches' => fn ($query) => $query
                ->with(['homeTeam', 'awayTeam'])
                ->latest(),
        ]);

        $savedTeams = Team::query()->orderBy('name')->get();

        return view('admin.competitions.edit', compact('competition', 'savedTeams'));
    }

    public function update(Request $request, Competition $competition): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'registration_deadline' => ['nullable', 'date'],
            'team_limit' => ['nullable', 'integer', 'min:1', 'max:999'],
            'entry_fee' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'description' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        $competition->update($data);

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

        $teamName = $data['name'] ?? null;

        if (! empty($data['saved_team_id'])) {
            $savedTeam = Team::query()->find($data['saved_team_id']);
            $teamName = $savedTeam?->name;
        }

        if (! $teamName) {
            return back()->withErrors(['name' => 'Norādi komandas nosaukumu vai izvēlies saglabātu komandu.']);
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
        ]);

        CompetitionMatch::create([
            'competition_id' => $competition->id,
            'home_team_id' => $data['home_team_id'],
            'away_team_id' => $data['away_team_id'],
            'home_score' => $data['home_score'],
            'away_score' => $data['away_score'],
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
}
