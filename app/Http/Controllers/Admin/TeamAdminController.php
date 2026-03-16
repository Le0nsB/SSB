<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeamAdminController extends Controller
{
    public function index(): View
    {
        $teams = Team::query()->orderBy('name')->get();
        return view('admin.teams.index', compact('teams'));
    }

    public function create(): View
    {
        return view('admin.teams.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Team::create($request->validate($this->teamRules()));
        return redirect()->route('admin.teams.index');
    }

    public function edit(Team $team): View
    {
        return view('admin.teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $team->update($request->validate($this->teamRules($team)));
        return redirect()->route('admin.teams.index');
    }

    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();
        return redirect()->route('admin.teams.index');
    }

    private function teamRules(?Team $team = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('teams', 'name')->ignore($team?->id)],
        ];
    }
}
