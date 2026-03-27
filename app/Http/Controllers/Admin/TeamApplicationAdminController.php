<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamApplicationAdminController extends Controller
{
    public function index(): View
    {
        $teamApplications = TeamApplication::query()
            ->latest()
            ->get();

        return view('admin.team-applications.index', compact('teamApplications'));
    }

    public function approve(TeamApplication $teamApplication): RedirectResponse
    {
        if ($teamApplication->status !== 'pending') {
            return redirect()
                ->route('admin.team-applications.index')
                ->with('error', 'Šis pieteikums jau ir apstrādāts.');
        }

        if (Team::query()->where('name', $teamApplication->name)->exists()) {
            return redirect()
                ->route('admin.team-applications.index')
                ->with('error', 'Komanda ar šādu nosaukumu jau eksistē.');
        }

        Team::create([
            'name' => $teamApplication->name,
            'logo_path' => $teamApplication->logo_path,
            'players' => $teamApplication->players,
        ]);

        $teamApplication->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()
            ->route('admin.team-applications.index')
            ->with('status', 'Pieteikums apstiprināts un komanda pievienota.');
    }

    public function reject(Request $request, TeamApplication $teamApplication): RedirectResponse
    {
        if ($teamApplication->status !== 'pending') {
            return redirect()
                ->route('admin.team-applications.index')
                ->with('error', 'Šis pieteikums jau ir apstrādāts.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $teamApplication->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        return redirect()
            ->route('admin.team-applications.index')
            ->with('status', 'Pieteikums noraidīts.');
    }
}
