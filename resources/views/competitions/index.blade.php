@extends('layouts.app')

@section('content')
    <h1 class="text-2xl md:text-3xl mb-2 ssb-title">Sacensības</h1>
    <p class="ssb-muted mb-6">Sekojiet līdzi turnīriem, pieteikšanās termiņiem un norises vietām.</p>

    @forelse ($competitions as $competition)
        <article class="ssb-card mb-4">
            @php
                $isFinished = method_exists($competition->event_date, 'lt') ? $competition->event_date->lt(now()->startOfDay()) : false;
                $teams = $competition->teams ?? collect();
                $matches = $competition->matches ?? collect();
                $placements = $teams->whereNotNull('final_position');
            @endphp

            <p class="text-sm ssb-muted">{{ $competition->event_date->format('d.m.Y') }}</p>
            <h2 class="font-semibold mt-1 text-zinc-100">{{ $competition->title }}</h2>
            <p class="ssb-text">{{ $competition->location }}</p>

            @if ($competition->description)
                <p class="ssb-text mt-3">{{ $competition->description }}</p>
            @endif

            @if ($competition->registration_deadline)
                <p class="text-sm ssb-accent mt-2">Pieteikšanās līdz {{ $competition->registration_deadline->format('d.m.Y') }}</p>
            @endif

            @if ($isFinished)
                <div class="mt-4">
                    <h3 class="text-lg ssb-subtitle">Spēļu rezultāti</h3>
                    @forelse ($matches as $match)
                        <p class="ssb-text mt-1">{{ $match->homeTeam?->name }} {{ $match->home_score }} : {{ $match->away_score }} {{ $match->awayTeam?->name }}</p>
                    @empty
                        <p class="ssb-muted mt-1">Rezultāti vēl nav pievienoti.</p>
                    @endforelse
                </div>

                <div class="mt-4">
                    <h3 class="text-lg ssb-subtitle">Gala vietas</h3>
                    @forelse ($placements as $team)
                        <p class="ssb-text mt-1">{{ $team->final_position }}. vieta — {{ $team->name }}</p>
                    @empty
                        <p class="ssb-muted mt-1">Gala vietas vēl nav pievienotas.</p>
                    @endforelse
                </div>
            @endif

            <p class="ssb-score mt-3">3x3 • PLAY FAST • PLAY TOGETHER</p>
        </article>
    @empty
        <p class="ssb-muted">Sacensību saraksts vēl nav pievienots.</p>
    @endforelse
@endsection
