<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeamApplicationController extends Controller
{
    public function create(): View
    {
        return view('team-applications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('teams', 'name'),
                Rule::unique('team_applications', 'name')->where(fn ($query) => $query->where('status', 'pending')),
            ],
            'logo' => ['nullable', 'image', 'max:2048'],
            'players' => ['required', 'string', 'max:3000'],
        ]);

        $players = $this->parsePlayers($validated['players']);

        if ($players->isEmpty()) {
            return back()
                ->withErrors(['players' => 'Ievadi vismaz vienu spēlētāju.'])
                ->withInput();
        }

        $logoPath = null;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('team-applications', 'public');
        }

        TeamApplication::create([
            'name' => $validated['name'],
            'logo_path' => $logoPath,
            'players' => $players->values()->all(),
            'status' => 'pending',
        ]);

        return redirect()
            ->route('team-applications.create')
            ->with('status', 'Komandas pieteikums nosūtīts. Admins to izskatīs un apstiprinās.');
    }

    private function parsePlayers(string $rawPlayers): Collection
    {
        return collect(preg_split('/\r\n|\r|\n/', $rawPlayers))
            ->map(fn (string $name) => trim($name))
            ->filter(fn (string $name) => $name !== '')
            ->unique();
    }
}
