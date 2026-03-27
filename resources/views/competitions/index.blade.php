@extends('layouts.app')

@section('content')
    <h1 class="text-2xl md:text-3xl mb-2 ssb-title">Sacensības</h1>
    <p class="ssb-muted mb-6">Sekojiet līdzi turnīriem, pieteikšanās termiņiem un norises vietām.</p>

    @forelse ($competitions as $competition)
        <article class="ssb-card mb-4 relative">
            @php
                $isFinished = method_exists($competition->event_date, 'lt') ? $competition->event_date->lt(now()->startOfDay()) : false;
                $teams = $competition->teams ?? collect();
                $matches = $competition->matches ?? collect();
                $placements = $teams->whereNotNull('final_position');
                $competitionId = data_get($competition, 'id');
                $standings = $competitionId ? ($standingsByCompetition[$competitionId] ?? collect()) : collect();
            @endphp

            <details class="ssb-info-popover">
                <summary aria-label="Turnīra tabulas saīsinājumu skaidrojums" title="Turnīra tabulas skaidrojums">i</summary>
                <div class="ssb-info-popover-panel">
                    <p class="font-semibold ssb-text">Turnīra tabulas skaidrojums</p>
                    <ul class="mt-2 space-y-1 text-xs ssb-muted">
                        <li><span class="ssb-text font-semibold">SP</span> - Spēles</li>
                        <li><span class="ssb-text font-semibold">U</span> - Uzvaras</li>
                        <li><span class="ssb-text font-semibold">Z</span> - Zaudējumi</li>
                        <li><span class="ssb-text font-semibold">PF</span> - Gūtie punkti</li>
                        <li><span class="ssb-text font-semibold">PA</span> - Ielaistie punkti</li>
                        <li><span class="ssb-text font-semibold">+/-</span> - Punktu starpība</li>
                        <li><span class="ssb-text font-semibold">P</span> - Turnīra punkti</li>
                    </ul>
                </div>
            </details>

            <p class="text-sm ssb-muted">{{ $competition->event_date->format('d.m.Y') }}</p>
            <h2 class="font-semibold mt-1 ssb-text">{{ $competition->title }}</h2>
            <p class="ssb-text">{{ $competition->location }}</p>

            @if ($competition->description)
                <p class="ssb-text mt-3">{{ $competition->description }}</p>
            @endif

            @if ($competition->registration_deadline)
                <p class="text-sm ssb-accent mt-2">Pieteikšanās līdz {{ $competition->registration_deadline->format('d.m.Y') }}</p>
            @endif

            @if ($matches->isNotEmpty())
                <div class="mt-4">
                    <h3 class="text-lg ssb-subtitle">Spēļu rezultāti</h3>
                    @forelse ($matches as $match)
                        <p class="ssb-text mt-1">
                            {{ $stageOptions[$match->stage] ?? 'Spēle' }}:
                            {{ $match->homeTeam?->name }} {{ $match->home_score }} : {{ $match->away_score }} {{ $match->awayTeam?->name }}
                            @if ($match->played_at)
                                <span class="ssb-muted">({{ $match->played_at->format('d.m.Y H:i') }})</span>
                            @endif
                        </p>
                    @empty
                        <p class="ssb-muted mt-1">Rezultāti vēl nav pievienoti.</p>
                    @endforelse
                </div>

                @if ($standings->isNotEmpty())
                    <div class="mt-4 overflow-x-auto">
                        <h3 class="text-lg ssb-subtitle">Turnīra tabula</h3>
                        <table class="w-full text-sm mt-2">
                            <thead>
                                <tr class="text-left ssb-muted">
                                    <th class="py-2 pr-3">Komanda</th>
                                    <th class="py-2 pr-3">SP</th>
                                    <th class="py-2 pr-3">U</th>
                                    <th class="py-2 pr-3">Z</th>
                                    <th class="py-2 pr-3">PF</th>
                                    <th class="py-2 pr-3">PA</th>
                                    <th class="py-2 pr-3">+/-</th>
                                    <th class="py-2 pr-0">P</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($standings as $row)
                                    <tr class="border-t border-zinc-800/70">
                                        <td class="py-2 pr-3 ssb-text">{{ $row['team']->name }}</td>
                                        <td class="py-2 pr-3 ssb-text">{{ $row['played'] }}</td>
                                        <td class="py-2 pr-3 ssb-text">{{ $row['wins'] }}</td>
                                        <td class="py-2 pr-3 ssb-text">{{ $row['losses'] }}</td>
                                        <td class="py-2 pr-3 ssb-text">{{ $row['points_for'] }}</td>
                                        <td class="py-2 pr-3 ssb-text">{{ $row['points_against'] }}</td>
                                        <td class="py-2 pr-3 ssb-text">{{ $row['diff'] }}</td>
                                        <td class="py-2 pr-0 ssb-accent font-semibold">{{ $row['table_points'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif

            @if ($isFinished)
                <div class="mt-4">
                    <h3 class="text-lg ssb-subtitle">Gala vietas</h3>
                    @forelse ($placements as $team)
                        <p class="ssb-text mt-1">{{ $team->final_position }}. vieta — {{ $team->name }}</p>
                    @empty
                        <p class="ssb-muted mt-1">Gala vietas vēl nav pievienotas.</p>
                    @endforelse
                </div>
            @endif

        </article>
    @empty
        <p class="ssb-muted">Sacensību saraksts vēl nav pievienots.</p>
    @endforelse
@endsection
